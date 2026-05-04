<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiTextService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.google.api_key');
        $this->model = config('services.google.text_model', 'gemini-2.0-flash');
    }

    public function generateCaption(string $businessName, string $topic, string $objective, string $tone, string $language): string
    {
        $prompt = "Write a compelling social media ad caption for the following:
Business: {$businessName}
Product/Topic: {$topic}
Objective: {$objective}
Tone: {$tone}
Language: {$language}
Max length: 150 characters
Include relevant emojis and a call to action.";

        return $this->ask($prompt) ?? '';
    }

    public function generateSuggestions(array $campaignData): array
    {
        $context = implode("\n", array_map(
            fn ($k, $v) => "{$k}: {$v}",
            array_keys($campaignData),
            array_values($campaignData)
        ));

        $prompt = "You are a Meta Ads expert. Analyze this campaign data and return ONLY a valid JSON array
with 2-3 objects: [{\"type\": \"budget|audience|creative|copy|general\", \"priority\": \"low|medium|high\", \"suggestion\": \"...\"}]

Campaign data:
{$context}";

        $raw = $this->ask($prompt, 'application/json');

        return json_decode($raw ?? '[]', true) ?? [];
    }

    public function reviewCampaign(string $fullCampaignJson): array
    {
        $prompt = "You are a senior Meta Ads strategist. Analyze this campaign performance data
and return ONLY valid JSON with this structure:
{
  \"overall_health\": \"good|warning|critical\",
  \"summary\": \"2-sentence plain English summary\",
  \"actions\": [
    {
      \"type\": \"pause_ad|increase_budget|decrease_budget|refresh_creative|change_audience|change_copy\",
      \"target\": \"campaign|ad_set|ad\",
      \"target_id\": \"local DB id\",
      \"reason\": \"plain English reason\",
      \"priority\": \"low|medium|high\",
      \"meta_change\": {}
    }
  ]
}

Campaign data:
{$fullCampaignJson}";

        $raw = $this->ask($prompt, 'application/json');

        return json_decode($raw ?? '{}', true) ?? [];
    }

    private function ask(string $prompt, ?string $responseMimeType = null): ?string
    {
        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]],
        ];

        if ($responseMimeType) {
            $payload['generationConfig'] = ['responseMimeType' => $responseMimeType];
        }

        $response = Http::withHeaders([
                'Content-Type'    => 'application/json',
                'x-goog-api-key' => $this->apiKey,
            ])
            ->timeout(180)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent", $payload);

        if (! $response->successful()) {
            return null;
        }

        return $response->json('candidates.0.content.parts.0.text');
    }
}
