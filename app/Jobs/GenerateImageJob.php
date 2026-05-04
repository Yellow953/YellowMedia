<?php

namespace App\Jobs;

use App\Models\GeneratedImage;
use App\Services\GeminiImageService;
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

    public function handle(GeminiImageService $service): void
    {
        $prompt = $service->buildPrompt($this->params);
        $path = $service->generate($prompt, (string) $this->image->tenant_id, $this->params['format'] ?? 'post');

        if ($path) {
            $this->image->update([
                'revised_prompt' => $prompt,
                'file_path' => $path,
                'status' => 'done',
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
