<?php

namespace App\Http\Controllers;

use App\Jobs\EditImageJob;
use App\Jobs\GenerateImageJob;
use App\Models\BrandProfile;
use App\Models\GeneratedImage;
use App\Services\GeminiTextService;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaStudioController extends Controller
{
    public function index(Request $request)
    {
        $brandProfile = BrandProfile::where('tenant_id', $request->user()->tenant_id)->first();
        $editImage    = null;

        if ($request->filled('edit')) {
            $editImage = GeneratedImage::where('tenant_id', $request->user()->tenant_id)
                ->find($request->integer('edit'));
        }

        return view('media-studio.index', compact('brandProfile', 'editImage'));
    }

    public function generate(Request $request, PlanLimitService $limits)
    {
        $request->validate([
            'topic'        => 'required|string|max:500',
            'format'       => 'required|in:post,square,story,banner',
            'style'        => 'required|in:realistic,illustrated,minimalist,bold',
            'color_hint'   => 'nullable|string|max:100',
            'text_overlay' => 'nullable|string|max:200',
            'language'     => 'nullable|string|max:50',
            'count'        => 'nullable|integer|min:1|max:4',
        ]);

        $user  = $request->user();
        $count = (int) $request->input('count', 1);

        if (! $limits->canGenerateImage($user->tenant)) {
            $plan = $user->tenant->planLimits();
            return response()->json([
                'error' => "You've reached your {$plan['images_per_month']}-image monthly limit on the {$plan['name']} plan.",
            ], 422);
        }

        $data = $request->only('topic', 'format', 'style', 'color_hint', 'text_overlay', 'language');
        $ids  = [];

        for ($i = 0; $i < $count; $i++) {
            $image = GeneratedImage::create([
                'tenant_id'         => $user->tenant_id,
                'user_id'           => $user->id,
                'prompt'            => $data['topic'],
                'format'            => $data['format'],
                'status'            => 'pending',
                'generation_params' => $data,
            ]);

            GenerateImageJob::dispatch($image, $data);
            $ids[] = $image->id;
        }

        return response()->json(['ids' => $ids]);
    }

    public function status(int $id, Request $request)
    {
        $image = GeneratedImage::where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);

        return response()->json([
            'status'  => $image->status,
            'url'     => $image->url,
            'caption' => $image->caption,
        ]);
    }

    public function library(Request $request)
    {
        $images = GeneratedImage::where('tenant_id', $request->user()->tenant_id)
            ->where('status', 'done')
            ->latest()
            ->paginate(12);

        return view('media-studio.library', compact('images'));
    }

    public function promptIdeas(Request $request, GeminiTextService $gemini)
    {
        $brandProfile = BrandProfile::where('tenant_id', $request->user()->tenant_id)->first();

        if (! $brandProfile || ! $brandProfile->hasVoice()) {
            return response()->json(['error' => 'Please set up your Brand Profile first so the AI knows your business.'], 422);
        }

        $topic = $request->string('topic')->trim()->value();

        $prompts = $gemini->generatePostPrompts($brandProfile, $topic ?: null);

        return response()->json(['prompts' => $prompts]);
    }

    public function edit(Request $request, PlanLimitService $limits)
    {
        $request->validate([
            'edit_prompt' => 'required|string|max:500',
            'format'      => 'nullable|in:post,square,story,banner',
            'image'       => 'nullable|file|image|max:10240',
            'image_id'    => 'nullable|integer',
        ]);

        $user = $request->user();

        if (! $limits->canGenerateImage($user->tenant)) {
            $plan = $user->tenant->planLimits();
            return response()->json([
                'error' => "You've reached your {$plan['images_per_month']}-image monthly limit on the {$plan['name']} plan.",
            ], 422);
        }

        // Resolve source image and save to a temp path (binary can't be JSON-serialised in the queue payload)
        if ($request->hasFile('image')) {
            $file       = $request->file('image');
            $sourceMime = $file->getMimeType() ?? 'image/jpeg';
            $ext        = $file->extension() ?: 'jpg';
            $tempPath   = 'edit-tmp/' . uniqid() . '.' . $ext;
            Storage::disk('public')->put($tempPath, file_get_contents($file->getRealPath()));
            $format = $request->input('format', 'post');
        } elseif ($request->filled('image_id')) {
            $source = GeneratedImage::where('tenant_id', $user->tenant_id)
                ->where('status', 'done')
                ->findOrFail($request->integer('image_id'));

            $tempPath   = $source->file_path;
            $sourceMime = str_ends_with($source->file_path, '.jpg') ? 'image/jpeg' : 'image/png';
            $format     = $request->input('format', $source->format);
        } else {
            return response()->json(['error' => 'Provide an image file or an image_id.'], 422);
        }

        $image = GeneratedImage::create([
            'tenant_id'         => $user->tenant_id,
            'user_id'           => $user->id,
            'prompt'            => $request->input('edit_prompt'),
            'format'            => $format,
            'status'            => 'pending',
            'generation_params' => ['edit_prompt' => $request->input('edit_prompt'), 'format' => $format],
        ]);

        EditImageJob::dispatch($image, $tempPath, $sourceMime, $request->input('edit_prompt'));

        return response()->json(['id' => $image->id]);
    }

    public function destroy(int $id, Request $request)
    {
        $image = GeneratedImage::where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);

        if ($image->file_path) {
            Storage::disk('public')->delete($image->file_path);
        }

        $image->delete();

        return redirect()->route('media.library')->with('success', 'Image deleted.');
    }
}
