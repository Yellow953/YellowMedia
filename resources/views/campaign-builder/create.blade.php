@extends('layouts.app')

@section('title', 'New Campaign')
@section('page-title', 'New Campaign')

@section('topbar-actions')
    <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
@endsection

@section('content')

{{-- Step Indicator --}}
<div class="d-flex align-items-center gap-2 mb-4" id="stepIndicator">
    @foreach(['Campaign', 'Ad Set', 'Creative', 'Review'] as $i => $label)
        <div class="d-flex align-items-center gap-2">
            <div class="step-dot d-flex align-items-center justify-content-center rounded-circle fw-bold"
                 id="step-dot-{{ $i+1 }}"
                 style="width:32px;height:32px;font-size:.8rem;background:{{ $i === 0 ? 'var(--yellow)' : '#e9ecef' }};color:{{ $i === 0 ? 'var(--black)' : '#6c757d' }};">
                {{ $i+1 }}
            </div>
            <span style="font-size:.875rem;font-weight:{{ $i === 0 ? '600' : '400' }};color:{{ $i === 0 ? 'var(--black)' : '#6c757d' }};" id="step-label-{{ $i+1 }}">{{ $label }}</span>
            @if($i < 3)
                <div style="width:40px;height:1px;background:#e9ecef;"></div>
            @endif
        </div>
    @endforeach
</div>

<form method="POST" action="{{ route('campaigns.store') }}" id="campaignForm">
@csrf

{{-- Step 1: Campaign --}}
<div id="step1" class="step-content">
    <div class="card">
        <div class="card-header py-3 px-4">Step 1 — Campaign Details</div>
        <div class="card-body p-4" style="max-width:600px;">

            @if($adAccounts->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No Meta Ads accounts connected. <a href="{{ route('settings.ad-accounts') }}">Connect one first.</a>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label fw-semibold">Campaign Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Summer Sale 2026" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ad Account <span class="text-danger">*</span></label>
                <select name="ad_account_id" class="form-select" required>
                    <option value="">Select an ad account…</option>
                    @foreach($adAccounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Objective <span class="text-danger">*</span></label>
                <select name="objective" class="form-select" required>
                    <option value="OUTCOME_TRAFFIC">Traffic</option>
                    <option value="OUTCOME_AWARENESS">Awareness</option>
                    <option value="OUTCOME_ENGAGEMENT">Engagement</option>
                    <option value="OUTCOME_LEADS">Lead Generation</option>
                    <option value="OUTCOME_SALES">Sales / Conversions</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Daily Budget (USD)</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="budget" class="form-control" min="1" step="0.01" placeholder="20.00">
                </div>
            </div>

            <button type="button" class="btn btn-yellow" onclick="nextStep(2)">
                Next: Ad Set <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </div>
</div>

{{-- Step 2: Ad Set --}}
<div id="step2" class="step-content d-none">
    <div class="card">
        <div class="card-header py-3 px-4">Step 2 — Ad Set</div>
        <div class="card-body p-4" style="max-width:600px;">

            <div class="mb-3">
                <label class="form-label fw-semibold">Ad Set Name</label>
                <input type="text" name="ad_set_name" class="form-control" placeholder="e.g. UAE — 25-44">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold">Min Age</label>
                    <input type="number" name="age_min" class="form-control" value="18" min="13" max="65">
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold">Max Age</label>
                    <input type="number" name="age_max" class="form-control" value="44" min="13" max="65">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Countries</label>
                <input type="text" name="countries" class="form-control" placeholder="e.g. AE, SA, KW">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Placements</label>
                <select name="placement" class="form-select">
                    <option value="automatic">Automatic (Recommended)</option>
                    <option value="feed">Feed Only</option>
                    <option value="stories">Stories</option>
                    <option value="reels">Reels</option>
                </select>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold">End Date <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-yellow" onclick="nextStep(3)">
                    Next: Creative <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Step 3: Creative --}}
<div id="step3" class="step-content d-none">
    <div class="card">
        <div class="card-header py-3 px-4">Step 3 — Ad Creative</div>
        <div class="card-body p-4" style="max-width:700px;">

            <div class="mb-3">
                <label class="form-label fw-semibold">Select Image from Library</label>
                <div class="row g-2" style="max-height:220px;overflow-y:auto;">
                    @forelse($images as $image)
                        <div class="col-3">
                            <label class="d-block cursor-pointer">
                                <input type="radio" name="image_id" value="{{ $image->id }}" class="d-none image-radio">
                                <div class="border rounded overflow-hidden image-thumb" style="aspect-ratio:1;background:#f3f4f6;">
                                    @if($image->url)
                                        <img src="{{ $image->url }}" class="w-100 h-100 object-fit-cover">
                                    @endif
                                </div>
                            </label>
                        </div>
                    @empty
                        <div class="col-12 text-muted small">
                            No images in library. <a href="{{ route('media.index') }}" target="_blank">Generate one first.</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Headline</label>
                <input type="text" name="headline" class="form-control" placeholder="e.g. Shop the Summer Sale — Up to 50% Off">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ad Copy (Body)</label>
                <textarea name="body" class="form-control" rows="3" placeholder="Write your ad copy here…"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Caption</label>
                <div class="input-group">
                    <textarea name="caption" id="captionInput" class="form-control" rows="2" placeholder="AI-generated or write your own…"></textarea>
                    <button type="button" class="btn btn-outline-secondary" id="captionBtn" onclick="generateCaption()">
                        <i class="bi bi-magic me-1"></i> Generate with AI
                    </button>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-semibold">CTA Button</label>
                    <select name="cta" class="form-select">
                        <option value="LEARN_MORE">Learn More</option>
                        <option value="SHOP_NOW">Shop Now</option>
                        <option value="SIGN_UP">Sign Up</option>
                        <option value="CONTACT_US">Contact Us</option>
                        <option value="GET_OFFER">Get Offer</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold">Destination URL</label>
                    <input type="url" name="destination_url" class="form-control" placeholder="https://…">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-yellow" onclick="nextStep(4)">
                    Next: Review <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Step 4: Review --}}
<div id="step4" class="step-content d-none">
    <div class="card">
        <div class="card-header py-3 px-4">Step 4 — Review & Save</div>
        <div class="card-body p-4">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Your campaign will be saved as a <strong>draft</strong>. You can launch it to Meta from the campaign detail page.
            </div>

            <div id="reviewSummary" class="mb-4" style="font-size:.875rem;">
                {{-- populated by JS --}}
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(3)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="submit" class="btn btn-yellow">
                    <i class="bi bi-save me-1"></i> Save Campaign
                </button>
            </div>
        </div>
    </div>
</div>

</form>
@endsection

@push('styles')
<style>
.image-radio:checked + .image-thumb { outline: 3px solid var(--yellow); }
.image-thumb { transition: outline 0.1s; }
</style>
@endpush

@push('scripts')
<script>
let currentStep = 1;

function nextStep(n) { goToStep(n); }
function prevStep(n) { goToStep(n); }

function goToStep(n) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
    document.getElementById('step' + n).classList.remove('d-none');

    for (let i = 1; i <= 4; i++) {
        const dot = document.getElementById('step-dot-' + i);
        const label = document.getElementById('step-label-' + i);
        if (i === n) {
            dot.style.background = 'var(--yellow)'; dot.style.color = 'var(--black)';
            label.style.fontWeight = '600'; label.style.color = 'var(--black)';
        } else {
            dot.style.background = '#e9ecef'; dot.style.color = '#6c757d';
            label.style.fontWeight = '400'; label.style.color = '#6c757d';
        }
    }

    currentStep = n;
    if (n === 4) buildReview();
}

function buildReview() {
    const form = document.getElementById('campaignForm');
    const data = Object.fromEntries(new FormData(form).entries());
    document.getElementById('reviewSummary').innerHTML = `
        <div class="row g-2">
            <div class="col-md-6"><strong>Campaign Name:</strong> ${data.name || '—'}</div>
            <div class="col-md-6"><strong>Objective:</strong> ${data.objective || '—'}</div>
            <div class="col-md-6"><strong>Budget:</strong> $${data.budget || '—'}/day</div>
            <div class="col-md-6"><strong>Placement:</strong> ${data.placement || '—'}</div>
            <div class="col-md-6"><strong>Headline:</strong> ${data.headline || '—'}</div>
            <div class="col-md-6"><strong>CTA:</strong> ${data.cta || '—'}</div>
        </div>`;
}

async function generateCaption() {
    const btn = document.getElementById('captionBtn');
    const topic = document.querySelector('[name="name"]')?.value || '';
    const objective = document.querySelector('[name="objective"]')?.value || '';

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating…';

    try {
        const res = await fetch('{{ route("campaigns.caption") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ topic, objective, tone: 'professional', language: 'English' }),
        });
        const data = await res.json();
        document.getElementById('captionInput').value = data.caption || '';
    } catch (e) {}

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-magic me-1"></i> Generate with AI';
}

document.querySelectorAll('.image-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.image-thumb').forEach(t => t.style.outline = '');
        if (this.checked) this.nextElementSibling.style.outline = '3px solid var(--yellow)';
    });
});
</script>
@endpush
