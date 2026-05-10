<?php

namespace App\Services;

use App\Models\BrandProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiImageService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.google.api_key');
        $this->model  = config('services.google.image_model', 'gemini-3-pro-image-preview');
    }

    public function generate(string $prompt, string $tenantId, string $format = 'post'): ?string
    {
        $aspectRatio = match ($format) {
            'square' => '1:1',
            'story'  => '9:16',
            'banner' => '16:9',
            default  => '4:5',
        };

        $response = Http::withHeaders([
                'Content-Type'    => 'application/json',
                'x-goog-api-key' => $this->apiKey,
            ])
            ->timeout(120)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent", [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'responseModalities' => ['IMAGE', 'TEXT'],
                    'imageConfig'        => ['aspectRatio' => $aspectRatio],
                ],
            ]);

        if (! $response->successful()) {
            Log::error('Gemini image API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        $parts = $response->json('candidates.0.content.parts') ?? [];

        foreach ($parts as $part) {
            if (isset($part['inlineData'])) {
                $imageData = base64_decode($part['inlineData']['data']);
                $extension = $this->mimeToExtension($part['inlineData']['mimeType'] ?? 'image/png');
                $path = "generated/{$tenantId}/" . uniqid() . ".{$extension}";
                Storage::disk('public')->put($path, $imageData);

                return $path;
            }
        }

        // API responded but returned no image (safety filter, text-only response, etc.)
        Log::warning('Gemini image API returned no image part', [
            'finish_reason' => $response->json('candidates.0.finishReason'),
            'text_parts'    => collect($parts)->whereNotNull('text')->pluck('text')->implode(' | '),
            'safety'        => $response->json('candidates.0.safetyRatings'),
        ]);

        return null;
    }

    public function buildPrompt(array $params, ?BrandProfile $brandProfile = null): string
    {
        $sanitize    = fn(string $s) => str_replace('"', "'", $s);
        $topic       = $sanitize($params['topic'] ?? '');
        $format      = $params['format'] ?? 'post';
        $style       = $params['style'] ?? 'professional';
        $colorHint   = $sanitize($params['color_hint'] ?? '');
        $textOverlay = $sanitize($params['text_overlay'] ?? '');
        $language    = $params['language'] ?? 'English';

        $noLogoRule    = "IMPORTANT: Do NOT include any brand name, logo, wordmark, or brand identity — no text that identifies a specific brand or company.";
        $noHashtagRule = "IMPORTANT: Do NOT include any hashtags (# symbols) anywhere in the image — not in overlays, captions, or any text element.";
        $noFakeRule    = "IMPORTANT: This must look like a professionally designed POSTER or PRINT AD — clean, intentional, typographic-led. STRICTLY FORBIDDEN: glowing light rays, golden sparkles, lens flares, bokeh, 3D floating objects, phone/tablet mockups with screenshots floating in space, WhatsApp or app icons pasted as overlays, AI-generated people holding devices, dark backgrounds with neon/gold glow effects, and any element that looks digitally composited rather than designed. If dark: use flat solid black with bold clean typography only. If light: use clean white or yellow with crisp graphic elements only.";
        $realisticRule = "All food, products, and physical items must look photorealistic — high-quality studio photography style, not illustrated or 3D-rendered.";

        [$bg, $layout] = $this->randomDesignVariant($colorHint);

        $creativityRule = "DESIGN VARIANT FOR THIS GENERATION (treat this as a print poster/ad brief):
- Background: {$bg}
- Layout style: {$layout}
Execute this as a clean graphic design — flat colors, bold typography, crisp edges. No glowing effects, no 3D renders, no floating UI elements. Think: billboard ad or magazine print, not a social media AI composite.";

        // If we have a brand profile with a visual style, build a brand-specific prompt
        if ($brandProfile && $brandProfile->voice_summary) {
            $textLine = $textOverlay
                ? "Include this text overlay in the image: '{$textOverlay}'"
                : "Do not add text overlays unless they are part of the visual concept.";

            return "Create a professional social media marketing image ({$format}).

CONCEPT / TOPIC: {$topic}

BRAND VISUAL STYLE:
{$brandProfile->voice_summary}

DESIGN INSTRUCTIONS:
- Style: {$style}
- Language for any text: {$language}
- {$textLine}
- {$creativityRule}
- {$noLogoRule}
- {$noHashtagRule}
- {$noFakeRule}
- {$realisticRule}
- No watermarks, no borders, no frames";
        }

        // Generic fallback when no brand profile
        $colors   = $colorHint ?: 'yellow #F5C300, black #111111, and white — use these as the core palette';
        $textLine = $textOverlay ? "Text overlay to include: {$textOverlay}." : "No text overlay.";

        return "Create a {$style} social media marketing image for a {$format} post.

TOPIC: {$topic}

DESIGN INSTRUCTIONS:
- Core color palette: {$colors}
- Language for any text: {$language}
- {$textLine}
- {$creativityRule}
- {$noLogoRule}
- {$noHashtagRule}
- {$noFakeRule}
- {$realisticRule}
- High quality, eye-catching, modern design optimized for social media engagement
- No watermarks, no borders, no frames";
    }

    public function editImage(string $imageContents, string $mimeType, string $editPrompt, string $tenantId): ?string
    {
        $sanitizedPrompt = str_replace('"', "'", $editPrompt);

        // Resize to max 1024px on the longest side to keep the payload small
        $imageContents = $this->resizeImageContents($imageContents, $mimeType);

        $response = Http::withHeaders([
                'Content-Type'    => 'application/json',
                'x-goog-api-key' => $this->apiKey,
            ])
            ->timeout(180)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent", [
                'contents' => [[
                    'parts' => [
                        ['inlineData' => ['mimeType' => 'image/jpeg', 'data' => base64_encode($imageContents)]],
                        ['text' => "Edit this image as follows: {$sanitizedPrompt}. Keep the overall composition and style unless specifically asked to change it. Return the edited image."],
                    ],
                ]],
                'generationConfig' => [
                    'responseModalities' => ['IMAGE', 'TEXT'],
                ],
            ]);

        if (! $response->successful()) {
            Log::error('Gemini image edit API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        $parts = $response->json('candidates.0.content.parts') ?? [];

        foreach ($parts as $part) {
            if (isset($part['inlineData'])) {
                $imageData = base64_decode($part['inlineData']['data']);
                $extension = $this->mimeToExtension($part['inlineData']['mimeType'] ?? 'image/png');
                $path = "generated/{$tenantId}/" . uniqid() . ".{$extension}";
                Storage::disk('public')->put($path, $imageData);
                return $path;
            }
        }

        Log::warning('Gemini image edit returned no image part', [
            'finish_reason' => $response->json('candidates.0.finishReason'),
            'text_parts'    => collect($parts)->whereNotNull('text')->pluck('text')->implode(' | '),
        ]);

        return null;
    }

    private function randomDesignVariant(string $colorHint): array
    {
        $backgrounds = [
            'pure solid yellow (#F5C300) background — bold and vibrant, flat color only',
            'clean white background with yellow accent blocks or borders',
            'solid flat black background with bold white and yellow typography only — no glow, no sparkle, no gradients',
            'light warm off-white or cream background — minimal and airy',
            'split layout: left half solid yellow, right half solid black — hard geometric edge',
            'split layout: left half solid white, right half solid yellow — clean division',
            'solid yellow background with bold black geometric shapes and strong typography',
            'white background with a thick yellow border or yellow band at top/bottom',
        ];

        $layouts = [
            'large bold headline text dominating the top third, product or icon below — typographic-led design',
            'hero product or food close-up filling 60% of the frame, text in remaining space',
            'flat lay top-down shot with elements arranged neatly on a surface',
            'lifestyle scene — the product or service shown in real-world context',
            'icon grid layout — feature icons arranged in a clean row or grid with short labels',
            'split panel — two distinct zones for visual and text',
            'centered minimal composition with generous white/negative space',
            'dynamic diagonal composition with bold text at an angle',
            'testimonial or stat card style — a prominent number or quote as the focal point',
            'before/after or comparison layout with two panels',
        ];

        // If the user specified a color hint, respect it and only randomize layout
        if ($colorHint) {
            return [$colorHint, $layouts[array_rand($layouts)]];
        }

        return [$backgrounds[array_rand($backgrounds)], $layouts[array_rand($layouts)]];
    }

    private function resizeImageContents(string $contents, string $mimeType, int $maxDim = 1024): string
    {
        $src = @imagecreatefromstring($contents);
        if (! $src) return $contents;

        $w = imagesx($src);
        $h = imagesy($src);

        if ($w <= $maxDim && $h <= $maxDim) {
            imagedestroy($src);
            return $contents;
        }

        $scale = $maxDim / max($w, $h);
        $nw    = (int) round($w * $scale);
        $nh    = (int) round($h * $scale);

        $dst = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($src);

        ob_start();
        imagejpeg($dst, null, 88);
        $resized = ob_get_clean();
        imagedestroy($dst);

        return $resized ?: $contents;
    }

    private function mimeToExtension(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            default => 'png',
        };
    }
}
