<?php

namespace App\Jobs;

use App\Models\GeneratedImage;
use App\Services\GeminiImageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class EditImageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public int $timeout = 180;

    public function __construct(
        private readonly GeneratedImage $image,
        private readonly string $sourcePath,
        private readonly string $sourceMimeType,
        private readonly string $editPrompt
    ) {}

    public function handle(GeminiImageService $imageService): void
    {
        $sourceContents = Storage::disk('public')->get($this->sourcePath);

        if (! $sourceContents) {
            $this->image->update(['status' => 'failed']);
            return;
        }

        $path = $imageService->editImage(
            $sourceContents,
            $this->sourceMimeType,
            $this->editPrompt,
            (string) $this->image->tenant_id
        );

        // Clean up uploaded temp file (leave library images untouched)
        if (str_starts_with($this->sourcePath, 'edit-tmp/')) {
            Storage::disk('public')->delete($this->sourcePath);
        }

        if ($path) {
            $this->image->update(['file_path' => $path, 'status' => 'done']);
        } else {
            $this->image->update(['status' => 'failed']);
        }
    }

    public function failed(\Throwable $e): void
    {
        if (str_starts_with($this->sourcePath, 'edit-tmp/')) {
            Storage::disk('public')->delete($this->sourcePath);
        }
        $this->image->update(['status' => 'failed']);
    }
}
