<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateImageJob;
use App\Models\BrandProfile;
use App\Models\GeneratedImage;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaStudioController extends Controller
{
    public function index(Request $request)
    {
        $brandProfile = BrandProfile::where('tenant_id', $request->user()->tenant_id)->first();
        return view('media-studio.index', compact('brandProfile'));
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
        ]);

        $user = $request->user();

        if (! $limits->canGenerateImage($user->tenant)) {
            $plan = $user->tenant->planLimits();
            return response()->json([
                'error' => "You've reached your {$plan['images_per_month']}-image monthly limit on the {$plan['name']} plan.",
            ], 422);
        }

        $validated = $request->validated();

        $image = GeneratedImage::create([
            'tenant_id' => $user->tenant_id,
            'user_id'   => $user->id,
            'prompt'    => $validated['topic'],
            'format'    => $validated['format'],
            'status'    => 'pending',
        ]);

        GenerateImageJob::dispatch($image, $request->only('topic', 'format', 'style', 'color_hint', 'text_overlay', 'language'));

        return response()->json(['id' => $image->id]);
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
