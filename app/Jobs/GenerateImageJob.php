<?php

namespace App\Jobs;

use App\Models\BrandProfile;
use App\Models\GeneratedImage;
use App\Services\GeminiImageService;
use App\Services\GeminiTextService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateImageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 180;

    public function __construct(
        private readonly GeneratedImage $image,
        private readonly array $params
    ) {}

    public function handle(GeminiImageService $imageService, GeminiTextService $textService): void
    {
        $brandProfile = BrandProfile::where('tenant_id', $this->image->tenant_id)->first();

        $prompt = $imageService->buildPrompt($this->params, $brandProfile);
        $path = $imageService->generate($prompt, (string) $this->image->tenant_id, $this->params['format'] ?? 'post');

        if ($path) {
            $caption = $textService->generatePostCaption(
                $this->params['topic'] ?? '',
                $this->params['format'] ?? 'post',
                $this->params['language'] ?? 'English',
                $brandProfile
            );

            $this->image->update([
                'revised_prompt' => $prompt,
                'file_path'      => $path,
                'caption'        => $caption,
                'status'         => 'done',
            ]);
        } else {
            $this->image->update(['status' => 'failed']);
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->image->update(['status' => 'failed']);
    }
}
