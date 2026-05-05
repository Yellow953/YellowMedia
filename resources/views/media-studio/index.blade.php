@extends('layouts.app')

@section('title', 'Media Studio')
@section('page-title', 'Media Studio')

@section('topbar-actions')
    @if(!$brandProfile?->hasVoice())
        <a href="{{ route('settings.index', ['tab' => 'brand']) }}" class="btn btn-outline-warning btn-sm me-2">
            <i class="bi bi-palette me-1"></i> Set up Brand Profile
        </a>
    @endif
    <a href="{{ route('media.library') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-images me-1"></i> Library
    </a>
@endsection

@section('content')
<div class="row g-4">
    {{-- Generator Form --}}
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header py-3 px-4">Generate Image</div>
            <div class="card-body p-4">
                <form id="generateForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Post Topic / Description <span class="text-danger">*</span></label>
                        <textarea name="topic" id="topic" class="form-control" rows="3"
                                  placeholder="e.g. Summer sale promotion for a coffee shop with 30% discount"></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Format</label>
                            <select name="format" id="format" class="form-select">
                                <option value="post" selected>Instagram (4:5)</option>
                                <option value="square">Square (1:1)</option>
                                <option value="story">Story (9:16)</option>
                                <option value="banner">Banner (16:9)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Style</label>
                            <select name="style" id="style" class="form-select">
                                <option value="realistic">Realistic</option>
                                <option value="illustrated">Illustrated</option>
                                <option value="minimalist">Minimalist</option>
                                <option value="bold">Bold</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Brand Color Hint <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="color_hint" class="form-control" placeholder="e.g. Yellow and black, or #F5C300">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Text Overlay <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="text_overlay" class="form-control" placeholder="e.g. خصم 30% أو Summer Sale">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Language</label>
                        <select name="language" class="form-select">
                            <option value="English">English</option>
                            <option value="Arabic">Arabic</option>
                            <option value="French">French</option>
                        </select>
                    </div>

                    <button type="submit" id="generateBtn" class="btn btn-yellow w-100">
                        <i class="bi bi-magic me-1"></i> Generate Image
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Preview Panel --}}
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header py-3 px-4">Preview</div>
            <div class="card-body d-flex align-items-center justify-content-center p-4" style="min-height:400px;">

                {{-- Idle state --}}
                <div id="idleState" class="text-center text-muted">
                    <i class="bi bi-magic fs-1 d-block mb-3 opacity-25"></i>
                    <div class="fw-semibold">Your image will appear here</div>
                    <div class="small mt-1">Fill in the form and click Generate</div>
                </div>

                {{-- Loading state --}}
                <div id="loadingState" class="text-center d-none">
                    <div class="spinner-border mb-3" style="color: var(--yellow); width:3rem;height:3rem;" role="status"></div>
                    <div class="fw-semibold">Generating your image...</div>
                    <div class="small text-muted mt-1">This may take 20–60 seconds</div>
                </div>

                {{-- Result state --}}
                <div id="resultState" class="d-none w-100">
                    <img id="resultImage" src="" alt="Generated image" class="img-fluid rounded mx-auto d-block" style="max-height:420px;">

                    {{-- Caption box --}}
                    <div id="captionBox" class="d-none mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-semibold" style="font-size:.8rem;color:#4b5563;">
                                <i class="bi bi-chat-quote me-1"></i> Generated Caption
                            </span>
                            <button type="button" id="copyCaptionBtn" class="btn btn-outline-secondary btn-sm" style="font-size:.75rem;padding:.2rem .6rem;">
                                <i class="bi bi-copy me-1"></i> Copy
                            </button>
                        </div>
                        <div id="captionText" class="p-3 rounded"
                             style="background:#f9fafb;border:1px solid #e5e7eb;font-size:.875rem;line-height:1.6;white-space:pre-wrap;max-height:160px;overflow-y:auto;"></div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <a id="downloadBtn" href="#" download class="btn btn-yellow btn-sm">
                            <i class="bi bi-download me-1"></i> Download
                        </a>
                        <button onclick="resetForm()" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-repeat me-1"></i> Generate Another
                        </button>
                        <a href="{{ route('media.library') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-images me-1"></i> View Library
                        </a>
                    </div>
                </div>

                {{-- Error state --}}
                <div id="errorState" class="text-center d-none">
                    <i class="bi bi-exclamation-circle fs-1 d-block mb-3 text-danger opacity-50"></i>
                    <div class="fw-semibold text-danger">Generation failed</div>
                    <div class="small text-muted mt-1">Please try again</div>
                    <button onclick="resetForm()" class="btn btn-outline-secondary btn-sm mt-3">Try Again</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let pollingInterval = null;

document.getElementById('generateForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    if (!formData.get('topic').trim()) {
        alert('Please enter a topic or description.');
        return;
    }

    showState('loading');
    document.getElementById('generateBtn').disabled = true;

    try {
        const res = await fetch('{{ route("media.generate") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData,
        });
        const data = await res.json();

        if (data.id) {
            startPolling(data.id);
        } else {
            showState('error');
        }
    } catch (err) {
        showState('error');
    }
});

function startPolling(id) {
    pollingInterval = setInterval(async () => {
        try {
            const res = await fetch(`{{ url('/media-studio/status') }}/${id}`);
            const data = await res.json();

            if (data.status === 'done' && data.url) {
                clearInterval(pollingInterval);
                document.getElementById('resultImage').src = data.url;
                document.getElementById('downloadBtn').href = data.url;

                if (data.caption) {
                    document.getElementById('captionText').textContent = data.caption;
                    document.getElementById('captionBox').classList.remove('d-none');
                }

                showState('result');
            } else if (data.status === 'failed') {
                clearInterval(pollingInterval);
                showState('error');
            }
        } catch (err) {
            clearInterval(pollingInterval);
            showState('error');
        }
    }, 3000);
}

function showState(state) {
    ['idle','loading','result','error'].forEach(s => {
        document.getElementById(s + 'State').classList.toggle('d-none', s !== state);
    });
    if (state !== 'loading') {
        document.getElementById('generateBtn').disabled = false;
    }
}

function resetForm() {
    if (pollingInterval) clearInterval(pollingInterval);
    document.getElementById('captionBox').classList.add('d-none');
    document.getElementById('captionText').textContent = '';
    showState('idle');
}

document.getElementById('copyCaptionBtn')?.addEventListener('click', function() {
    const text = document.getElementById('captionText').textContent;
    navigator.clipboard.writeText(text).then(() => {
        this.innerHTML = '<i class="bi bi-check me-1"></i> Copied!';
        setTimeout(() => { this.innerHTML = '<i class="bi bi-copy me-1"></i> Copy'; }, 2000);
    });
});
</script>
@endpush
