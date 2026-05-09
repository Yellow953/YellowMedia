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
{{-- Post Prompt Ideas --}}
<div class="row g-4 mt-0">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="fw-semibold">Post Prompt Ideas</span>
                    <span class="text-muted small ms-2">AI suggestions based on your brand profile</span>
                </div>
                @if(!$brandProfile?->hasVoice())
                    <a href="{{ route('settings.index', ['tab' => 'brand']) }}" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-palette me-1"></i> Set up Brand Profile first
                    </a>
                @endif
            </div>
            <div class="card-body px-4 pb-4">

                @if($brandProfile?->hasVoice())
                <div class="d-flex gap-2 mb-4">
                    <input type="text" id="ideasTopic" class="form-control"
                           placeholder="Optional: describe a topic or feature (e.g. Ramadan offer, new delivery feature, summer sale)">
                    <button id="getIdeasBtn" class="btn btn-yellow text-nowrap">
                        <i class="bi bi-lightbulb me-1"></i> Get Ideas
                    </button>
                </div>
                @endif

                {{-- Idle --}}
                <div id="ideasIdle" class="text-center py-3 text-muted">
                    <i class="bi bi-lightbulb fs-2 d-block mb-2 opacity-25"></i>
                    <div class="small">Describe a topic or just click <strong>Get Ideas</strong> for general brand-based suggestions.</div>
                </div>

                {{-- Loading --}}
                <div id="ideasLoading" class="text-center py-4 d-none">
                    <div class="spinner-border spinner-border-sm me-2" style="color:var(--yellow);" role="status"></div>
                    <span class="text-muted small">Thinking up ideas for you…</span>
                </div>

                {{-- Results --}}
                <div id="ideasGrid" class="row g-3 d-none"></div>

                {{-- Error --}}
                <div id="ideasError" class="alert alert-danger d-none mb-0"></div>

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

// Post Prompt Ideas
const getIdeasBtn = document.getElementById('getIdeasBtn');
if (getIdeasBtn) {
    document.getElementById('ideasTopic')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') getIdeasBtn.click();
    });

    getIdeasBtn.addEventListener('click', async function () {
        document.getElementById('ideasIdle').classList.add('d-none');
        document.getElementById('ideasGrid').classList.add('d-none');
        document.getElementById('ideasError').classList.add('d-none');
        document.getElementById('ideasLoading').classList.remove('d-none');
        getIdeasBtn.disabled = true;

        const topic = document.getElementById('ideasTopic')?.value.trim() ?? '';
        const body  = new FormData();
        if (topic) body.append('topic', topic);

        try {
            const res = await fetch('{{ route("media.prompt-ideas") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body,
            });
            const data = await res.json();

            document.getElementById('ideasLoading').classList.add('d-none');

            if (!res.ok || data.error) {
                const err = document.getElementById('ideasError');
                err.textContent = data.error ?? 'Something went wrong. Please try again.';
                err.classList.remove('d-none');
            } else {
                const grid = document.getElementById('ideasGrid');
                grid.innerHTML = '';

                const formatLabels = { post: 'Post', story: 'Story', banner: 'Banner' };
                const formatIcons  = { post: 'bi-image', story: 'bi-phone', banner: 'bi-display' };
                const pillarColors = ['#F5C300', '#111111', '#6366f1', '#10b981', '#f97316', '#06b6d4'];

                (data.prompts ?? []).forEach((item, i) => {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-xl-4';
                    col.innerHTML = `
                        <div class="border rounded p-3 h-100 d-flex flex-column" style="background:#fafafa;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge" style="background:${pillarColors[i % pillarColors.length]};color:${i===1?'#fff':'#111'};font-size:.7rem;">${item.pillar ?? ''}</span>
                                <span class="text-muted small"><i class="bi ${formatIcons[item.format] ?? 'bi-image'} me-1"></i>${formatLabels[item.format] ?? 'Post'}</span>
                            </div>
                            <p class="mb-3 fw-semibold" style="font-size:.9rem;flex:1;">${item.prompt}</p>
                            <button class="btn btn-sm btn-outline-dark use-prompt-btn" data-prompt="${item.prompt.replace(/"/g, '&quot;')}" data-format="${item.format ?? 'post'}">
                                <i class="bi bi-arrow-up-left me-1"></i> Use this prompt
                            </button>
                        </div>`;
                    grid.appendChild(col);
                });

                grid.classList.remove('d-none');
            }
        } catch (e) {
            document.getElementById('ideasLoading').classList.add('d-none');
            const err = document.getElementById('ideasError');
            err.textContent = 'Failed to get ideas. Please try again.';
            err.classList.remove('d-none');
        } finally {
            getIdeasBtn.disabled = false;
        }
    });

    document.getElementById('ideasGrid').addEventListener('click', function (e) {
        const btn = e.target.closest('.use-prompt-btn');
        if (!btn) return;
        document.getElementById('topic').value = btn.dataset.prompt;
        document.getElementById('format').value = btn.dataset.format;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
</script>
@endpush
