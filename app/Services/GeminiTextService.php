<?php

namespace App\Services;

use App\Models\BrandProfile;
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

    public function generatePostCaption(string $topic, string $format, string $language, ?BrandProfile $brandProfile = null): string
    {
        $brandContext = $brandProfile ? $brandProfile->brandContext() : '';
        $hashtagLine = $brandProfile?->hashtags ? "Include these hashtags: {$brandProfile->hashtags}" : 'Include relevant hashtags.';

        $prompt = "Write a social media caption for a {$format} post.
Topic: {$topic}
Language: {$language}
{$brandContext}

Requirements:
- Ready to post directly on Instagram/Facebook
- Include relevant emojis
- End with a clear call to action
- {$hashtagLine}
- Output ONLY the caption text, nothing else, no explanations";

        return $this->ask($prompt) ?? '';
    }

    public function analyzeVoice(string $sampleCaptions, string $businessName): string
    {
        $prompt = "Analyze these social media captions from {$businessName} and write a concise brand voice summary (3-4 sentences) that captures:
- The tone and personality
- How they address their audience
- Common patterns, phrases, or styles
- Emoji usage and hashtag style

Sample captions:
{$sampleCaptions}

Output ONLY the brand voice summary, nothing else.";

        return $this->ask($prompt) ?? '';
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

    public function generatePostPrompts(BrandProfile $brandProfile, ?string $topic = null): array
    {
        $pillars = is_array($brandProfile->content_pillars)
            ? implode(', ', $brandProfile->content_pillars)
            : ($brandProfile->content_pillars ?? 'not specified');

        $topicLine = $topic
            ? "Focus specifically on this topic/feature the business wants to promote: {$topic}"
            : "Generate ideas across a variety of content pillars.";

        $prompt = "You are a social media content strategist helping a business plan their next Instagram posts.

Business: {$brandProfile->business_name}
Description: {$brandProfile->business_description}
Target audience: {$brandProfile->target_audience}
Tone: {$brandProfile->tone}
Content pillars: {$pillars}
Brand voice: {$brandProfile->voice_summary}

Sample past captions:
{$brandProfile->sample_captions}

{$topicLine}

Generate 6 specific, creative post prompt ideas that this business could use to generate an image right now.
Return ONLY a valid JSON array of objects:
[{\"prompt\": \"...\", \"pillar\": \"...\", \"format\": \"post|story|banner\"}]

Rules:
- Each prompt should be a clear description a designer could act on (not vague like \"motivational post\")
- Reference the actual business, product, or audience specifically
- Keep each prompt under 20 words
- No jargon, no hashtags in the prompt itself";

        $raw = $this->ask($prompt, 'application/json');

        return json_decode($raw ?? '[]', true) ?? [];
    }

    public function generateSuggestions(array $campaignData): array
    {
        $context = implode("\n", array_map(
            fn ($k, $v) => "{$k}: {$v}",
            array_keys($campaignData),
            array_values($campaignData)
        ));

        $prompt = "You are a friendly marketing assistant helping a small business owner who is not familiar with advertising terminology.
Analyze this campaign data and return ONLY a valid JSON array with 2-3 objects:
[{\"type\": \"budget|audience|creative|copy|general\", \"priority\": \"low|medium|high\", \"suggestion\": \"...\"}]

Rules for writing suggestions:
- Write in plain, conversational language — no jargon or abbreviations like CTR, CPC, ROAS, CPM, etc.
- If you must reference a metric, explain it in parentheses (e.g. \"cost per click (how much you pay each time someone taps your ad)\")
- Focus on what action to take and why it will help the business, not on the numbers themselves
- Be encouraging and practical — write as if advising a friend who runs a small business
- Keep each suggestion to 1-2 sentences

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
