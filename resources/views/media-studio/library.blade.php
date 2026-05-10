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
                    <div class="position-relative lb-trigger" style="aspect-ratio:1; background:#f3f4f6; overflow:hidden; cursor:pointer;"
                         data-url="{{ $image->url }}"
                         data-prompt="{{ $image->prompt }}"
                         data-caption="{{ $image->caption ?? '' }}">
                        @if($image->url)
                            <img src="{{ $image->url }}" alt="" class="w-100 h-100 object-fit-contain">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <i class="bi bi-image fs-2"></i>
                            </div>
                        @endif
                        <span class="position-absolute top-0 end-0 m-2 badge-draft">{{ ucfirst($image->format) }}</span>
                        @if($image->url)
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 hover-overlay" style="background:rgba(0,0,0,.35); transition:.2s;">
                                <i class="bi bi-eye text-white fs-2"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <div class="small text-muted mb-2 text-truncate" title="{{ $image->prompt }}">{{ $image->prompt }}</div>
                        <div class="d-flex gap-2">
                            @if($image->url)
                                <button type="button" class="btn btn-sm btn-yellow flex-fill lb-trigger"
                                        data-url="{{ $image->url }}"
                                        data-prompt="{{ $image->prompt }}"
                                        data-caption="{{ $image->caption ?? '' }}"
                                        title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-dark flex-fill open-edit-modal"
                                        data-id="{{ $image->id }}"
                                        data-url="{{ $image->url }}"
                                        title="Edit Image">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ $image->url }}" download class="btn btn-sm btn-outline-secondary flex-fill" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                            @endif
                            <a href="{{ route('campaigns.create') }}?image_id={{ $image->id }}" class="btn btn-sm btn-outline-secondary flex-fill" title="Use in Campaign">
                                <i class="bi bi-megaphone"></i>
                            </a>
                            <form method="POST" action="{{ route('media.destroy', $image->id) }}" onsubmit="return confirm('Delete this image?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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

{{-- Lightbox Modal --}}
<div class="modal fade" id="lightboxModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="background:#111;">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 pt-0 text-center">
                <img id="lightboxImg" src="" alt="" class="img-fluid rounded" style="max-height:70vh;">
                <div id="lightboxPrompt" class="text-white-50 small mt-3 px-2" style="font-style:italic;"></div>
                <div id="lightboxCaption" class="d-none mt-2 p-3 rounded text-start" style="background:rgba(255,255,255,.08);color:#e5e7eb;font-size:.875rem;line-height:1.6;"></div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <a id="lightboxDownload" href="#" download class="btn btn-yellow btn-sm">
                    <i class="bi bi-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>
{{-- Edit Image Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2"></i>Edit Image</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="rounded overflow-hidden" style="background:#f3f4f6;">
                            <img id="editModalImg" src="" alt="" class="img-fluid w-100" style="object-fit:contain;max-height:280px;">
                        </div>
                    </div>
                    <div class="col-md-7 d-flex flex-column">
                        <label class="form-label fw-semibold">What would you like to change?</label>
                        <textarea id="editModalPrompt" class="form-control flex-grow-1" rows="5"
                                  placeholder="e.g. Change the background to yellow, make the headline bigger, add a burger on the right, change text to Arabic..."></textarea>
                        <div id="editModalError" class="alert alert-danger py-2 mt-2 d-none"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="editModalBtn" class="btn btn-yellow btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> Apply Edits
                </button>
            </div>
            {{-- Result inside modal --}}
            <div id="editModalResult" class="d-none px-4 pb-4 text-center">
                <hr class="mt-0">
                <p class="small text-muted mb-2">Edited result — saved to your library</p>
                <img id="editResultImg" src="" alt="" class="img-fluid rounded" style="max-height:300px;">
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <a id="editResultDownload" href="#" download class="btn btn-yellow btn-sm">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal" onclick="location.reload()">
                        Done
                    </button>
                </div>
            </div>
            <div id="editModalLoading" class="d-none text-center py-4">
                <div class="spinner-border mb-2" style="color:var(--yellow);width:2.5rem;height:2.5rem;" role="status"></div>
                <div class="small text-muted">Applying edits… this may take 20–60 seconds</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-overlay { pointer-events: none; }
.position-relative:hover .hover-overlay { opacity: 1 !important; }
</style>
@endpush

@push('scripts')
<script>
let editPollingInterval = null;
let currentEditImageId  = null;

document.addEventListener('click', function (e) {
    // Lightbox
    const lb = e.target.closest('.lb-trigger');
    if (lb) {
        const url = lb.dataset.url;
        if (!url) return;
        document.getElementById('lightboxImg').src = url;
        document.getElementById('lightboxDownload').href = url;
        document.getElementById('lightboxPrompt').textContent = lb.dataset.prompt || '';
        const captionEl = document.getElementById('lightboxCaption');
        const caption = lb.dataset.caption || '';
        if (caption) { captionEl.textContent = caption; captionEl.classList.remove('d-none'); }
        else { captionEl.classList.add('d-none'); }
        new bootstrap.Modal(document.getElementById('lightboxModal')).show();
        return;
    }

    // Edit modal trigger
    const ed = e.target.closest('.open-edit-modal');
    if (ed) {
        currentEditImageId = ed.dataset.id;
        document.getElementById('editModalImg').src = ed.dataset.url;
        document.getElementById('editModalPrompt').value = '';
        document.getElementById('editModalError').classList.add('d-none');
        document.getElementById('editModalResult').classList.add('d-none');
        document.getElementById('editModalLoading').classList.add('d-none');
        document.getElementById('editModalBtn').disabled = false;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
});

document.getElementById('editModalBtn').addEventListener('click', async function () {
    const prompt = document.getElementById('editModalPrompt').value.trim();
    if (!prompt) { alert('Please describe what you want to change.'); return; }

    const errorEl = document.getElementById('editModalError');
    errorEl.classList.add('d-none');
    document.getElementById('editModalLoading').classList.remove('d-none');
    document.getElementById('editModalResult').classList.add('d-none');
    this.disabled = true;

    const body = new FormData();
    body.append('edit_prompt', prompt);
    body.append('image_id', currentEditImageId);

    try {
        const res  = await fetch('{{ route("media.edit") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body,
        });
        const data = await res.json();

        if (!res.ok || data.error) {
            document.getElementById('editModalLoading').classList.add('d-none');
            errorEl.textContent = data.error ?? 'Something went wrong.';
            errorEl.classList.remove('d-none');
            this.disabled = false;
            return;
        }

        startEditPolling(data.id);
    } catch (err) {
        document.getElementById('editModalLoading').classList.add('d-none');
        errorEl.textContent = 'Request failed. Please try again.';
        errorEl.classList.remove('d-none');
        this.disabled = false;
    }
});

function startEditPolling(id) {
    editPollingInterval = setInterval(async () => {
        try {
            const res  = await fetch(`{{ url('/media-studio/status') }}/${id}`);
            const data = await res.json();

            if (data.status === 'done' && data.url) {
                clearInterval(editPollingInterval);
                document.getElementById('editModalLoading').classList.add('d-none');
                document.getElementById('editResultImg').src = data.url;
                document.getElementById('editResultDownload').href = data.url;
                document.getElementById('editModalResult').classList.remove('d-none');
            } else if (data.status === 'failed') {
                clearInterval(editPollingInterval);
                document.getElementById('editModalLoading').classList.add('d-none');
                const errorEl = document.getElementById('editModalError');
                errorEl.textContent = 'Edit failed — try rephrasing your instructions.';
                errorEl.classList.remove('d-none');
                document.getElementById('editModalBtn').disabled = false;
            }
        } catch {
            clearInterval(editPollingInterval);
        }
    }, 3000);
}
</script>
@endpush
