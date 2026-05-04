<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateImageJob;
use App\Models\GeneratedImage;
use Illuminate\Http\Request;

class MediaStudioController extends Controller
{
    public function index()
    {
        return view('media-studio.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'format' => 'required|in:post,story,banner',
            'style' => 'required|in:realistic,illustrated,minimalist,bold',
            'color_hint' => 'nullable|string|max:100',
            'text_overlay' => 'nullable|string|max:200',
            'language' => 'nullable|string|max:50',
        ]);

        $user = $request->user();

        $image = GeneratedImage::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'prompt' => $request->topic,
            'format' => $request->format,
            'status' => 'pending',
        ]);

        GenerateImageJob::dispatch($image, $request->only('topic', 'format', 'style', 'color_hint', 'text_overlay', 'language'));

        return response()->json(['id' => $image->id]);
    }

    public function status(int $id, Request $request)
    {
        $image = GeneratedImage::where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);

        return response()->json([
            'status' => $image->status,
            'url' => $image->url,
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
            \Storage::disk('public')->delete($image->file_path);
        }

        $image->delete();

        return redirect()->route('media.library')->with('success', 'Image deleted.');
    }
}
