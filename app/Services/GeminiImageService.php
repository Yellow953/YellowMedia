<?php

namespace App\Services;

use App\Models\BrandProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GeminiImageService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.google.api_key');
        $this->model = config('services.google.image_model', 'gemini-3-pro-image-preview');
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

        return null;
    }

    public function buildPrompt(array $params, ?BrandProfile $brandProfile = null): string
    {
        $topic       = $params['topic'] ?? '';
        $format      = $params['format'] ?? 'post';
        $style       = $params['style'] ?? 'professional';
        $colorHint   = $params['color_hint'] ?? '';
        $textOverlay = $params['text_overlay'] ?? '';
        $language    = $params['language'] ?? 'English';

        // If we have a brand profile with a visual style, build a brand-specific prompt
        if ($brandProfile && $brandProfile->voice_summary) {
            $textLine = $textOverlay
                ? "Include this text overlay in the image: \"{$textOverlay}\""
                : "Do not add text overlays unless they are part of the visual concept.";

            return "Create a professional social media marketing image ({$format}) for {$brandProfile->business_name}.

CONCEPT / TOPIC: {$topic}

MANDATORY VISUAL STYLE — follow these instructions precisely:
{$brandProfile->voice_summary}

ADDITIONAL INSTRUCTIONS:
- Style variation requested: {$style}
- Language for any text in the image: {$language}
- {$textLine}
- Image must be photorealistic, high quality, suitable for professional social media advertising
- No watermarks, no borders, no frames";
        }

        // Generic fallback when no brand profile
        $colors = $colorHint ?: ($brandProfile?->brand_colors ?: 'brand-appropriate colors');
        $textLine = $textOverlay ? "Text overlay: {$textOverlay}." : "No text overlay.";

        return "Create a {$style} social media {$format} image about: {$topic}.
Use a professional look suitable for a business.
Color palette: {$colors}.
{$textLine}
Language for any text: {$language}.
High quality, eye-catching, modern marketing design.
Optimized for social media engagement.";
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
