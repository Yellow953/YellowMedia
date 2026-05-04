@extends('layouts.app')

@section('title', 'Media Library')
@section('page-title', 'Media Library')

@section('topbar-actions')
    <a href="{{ route('media.index') }}" class="btn btn-yellow btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Generate New
    </a>
@endsection

@section('content')
@if($images->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-images fs-1 d-block mb-3 opacity-25"></i>
            <div class="fw-semibold">Your media library is empty</div>
            <div class="small mt-1">Generated images will appear here</div>
            <a href="{{ route('media.index') }}" class="btn btn-yellow btn-sm mt-3">
                <i class="bi bi-magic me-1"></i> Generate First Image
            </a>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($images as $image)
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card h-100">
                    <div class="position-relative" style="aspect-ratio:1; background:#f3f4f6; overflow:hidden;">
                        @if($image->url)
                            <img src="{{ $image->url }}" alt="" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <i class="bi bi-image fs-2"></i>
                            </div>
                        @endif
                        <span class="position-absolute top-0 end-0 m-2 badge-draft">{{ ucfirst($image->format) }}</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="small text-muted mb-2 text-truncate" title="{{ $image->prompt }}">{{ $image->prompt }}</div>
                        <div class="d-flex gap-2">
                            @if($image->url)
                                <a href="{{ $image->url }}" download class="btn btn-sm btn-outline-secondary flex-fill">
                                    <i class="bi bi-download"></i>
                                </a>
                            @endif
                            <a href="{{ route('campaigns.create') }}?image_id={{ $image->id }}" class="btn btn-sm btn-outline-secondary flex-fill" title="Use in Campaign">
                                <i class="bi bi-megaphone"></i>
                            </a>
                            <form method="POST" action="{{ route('media.destroy', $image->id) }}" onsubmit="return confirm('Delete this image?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $images->links() }}
    </div>
@endif
@endsection
