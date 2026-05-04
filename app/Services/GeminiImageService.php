<?php

namespace App\Services;

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

    public function generate(string $prompt, string $tenantId): ?string
    {
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(120)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['responseModalities' => ['IMAGE', 'TEXT']],
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
                Storage::put($path, $imageData);

                return $path;
            }
        }

        return null;
    }

    public function buildPrompt(array $params): string
    {
        $style = $params['style'] ?? 'professional';
        $format = $params['format'] ?? 'post';
        $topic = $params['topic'] ?? '';
        $colorHint = $params['color_hint'] ?? 'brand colors';
        $textOverlay = $params['text_overlay'] ?? '';
        $language = $params['language'] ?? 'English';

        return "Create a {$style} social media {$format} image about: {$topic}.
Use a professional look suitable for a business.
Color palette: {$colorHint}.
Text overlay (if requested): {$textOverlay}.
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
