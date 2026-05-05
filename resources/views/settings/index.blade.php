@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@push('styles')
<style>
    .settings-nav .nav-link {
        color: #4b5563;
        font-size: .875rem;
        font-weight: 500;
        padding: .6rem 1rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: .6rem;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        transition: all .15s;
    }
    .settings-nav .nav-link i { font-size: 1rem; width: 1.1rem; }
    .settings-nav .nav-link:hover { background: #f3f4f6; color: #111; }
    .settings-nav .nav-link.active { background: #fef9c3; color: #111; font-weight: 600; }
    .settings-nav .nav-link.active i { color: var(--yellow-dark); }

    .settings-panel { display: none; }
    .settings-panel.active { display: block; }

    .section-label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #9ca3af;
        padding: .5rem 1rem .25rem;
    }

    .plan-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .3rem .75rem;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .plan-free     { background: #f3f4f6; color: #6b7280; }
    .plan-starter  { background: #dbeafe; color: #1e40af; }
    .plan-pro      { background: #fef9c3; color: #92400e; }

    .stat-mini { background: #f9fafb; border-radius: 10px; padding: 1rem 1.25rem; border: 1px solid #e5e7eb; }
    .stat-mini .val { font-size: 1.5rem; font-weight: 700; color: #111; line-height: 1; }
    .stat-mini .lbl { font-size: .75rem; color: #6b7280; margin-top: .2rem; }

    .account-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        background: #fff;
    }
    .account-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        background: #1877f2;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')

@php $activeTab = request('tab', 'profile'); @endphp

<div class="row g-4">

    {{-- Left nav --}}
    <div class="col-md-3">
        <div class="card p-2">
            <nav class="settings-nav d-flex flex-column gap-1">
                <div class="section-label">Account</div>
                <a href="?tab=profile"
                   class="nav-link {{ $activeTab === 'profile' ? 'active' : '' }}">
                    <i class="bi bi-person"></i> Profile
                </a>
                <a href="?tab=security"
                   class="nav-link {{ $activeTab === 'security' ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i> Security
                </a>

                <div class="section-label mt-1">Workspace</div>
                <a href="?tab=brand"
                   class="nav-link {{ $activeTab === 'brand' ? 'active' : '' }}">
                    <i class="bi bi-palette"></i> Brand Profile
                    @if(!$brandProfile?->hasVoice())
                        <span class="badge ms-auto" style="background:#fef3c7;color:#92400e;font-size:.65rem;">Setup</span>
                    @endif
                </a>
                <a href="?tab=connections"
                   class="nav-link {{ $activeTab === 'connections' ? 'active' : '' }}">
                    <i class="bi bi-plug"></i> Connections
                    @if($accounts->count())
                        <span class="badge ms-auto" style="background:var(--yellow);color:#111;font-size:.65rem;">{{ $accounts->count() }}</span>
                    @endif
                </a>
                <a href="?tab=organization"
                   class="nav-link {{ $activeTab === 'organization' ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Organization
                </a>
            </nav>
        </div>
    </div>

    {{-- Right panels --}}
    <div class="col-md-9">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Profile --}}
        <div class="settings-panel {{ $activeTab === 'profile' ? 'active' : '' }}">
            <div class="card">
                <div class="card-header py-3 px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-person text-muted"></i> Profile
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('settings.profile.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-center mb-4">
                                <div style="width:72px;height:72px;border-radius:50%;background:var(--yellow);display:flex;align-items:center;justify-content:center;font-size:1.75rem;font-weight:700;color:#111;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Email Address</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                            <div class="form-text">Email cannot be changed here.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Role</label>
                            <div class="form-control bg-light" style="color:#6b7280;">
                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                            </div>
                        </div>
                        <div class="mt-4 pt-2 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-yellow px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Security --}}
        <div class="settings-panel {{ $activeTab === 'security' ? 'active' : '' }}">
            <div class="card">
                <div class="card-header py-3 px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock text-muted"></i> Security
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('settings.password.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Current Password</label>
                            <input type="password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">New Password</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size:.875rem;">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-yellow px-4">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Brand Profile --}}
        <div class="settings-panel {{ $activeTab === 'brand' ? 'active' : '' }}">
            <div class="card">
                <div class="card-header py-3 px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-palette text-muted"></i> Brand Profile
                    <span class="ms-auto text-muted small">Used by AI when generating images and captions</span>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('settings.brand.save') }}">
                        @csrf @method('PUT')

                        <div class="row g-4">
                            {{-- Left column --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">Business Name</label>
                                    <input type="text" name="business_name" class="form-control"
                                           value="{{ old('business_name', $brandProfile?->business_name) }}"
                                           placeholder="e.g. YellowPOS">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">Business Description</label>
                                    <textarea name="business_description" class="form-control" rows="3"
                                              placeholder="What does your business do? Who do you serve?">{{ old('business_description', $brandProfile?->business_description) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">Target Audience</label>
                                    <input type="text" name="target_audience" class="form-control"
                                           value="{{ old('target_audience', $brandProfile?->target_audience) }}"
                                           placeholder="e.g. Restaurant owners and retail merchants in Lebanon">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">Tone</label>
                                    <select name="tone" class="form-select">
                                        @foreach(['professional','casual','friendly','bold','inspiring'] as $t)
                                            <option value="{{ $t }}" {{ old('tone', $brandProfile?->tone) === $t ? 'selected' : '' }}>
                                                {{ ucfirst($t) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">Brand Colors</label>
                                    <input type="text" name="brand_colors" class="form-control"
                                           value="{{ old('brand_colors', $brandProfile?->brand_colors) }}"
                                           placeholder="e.g. Yellow #F5C300 and Black #111111">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">Instagram Handle</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" name="instagram_handle" class="form-control"
                                               value="{{ old('instagram_handle', $brandProfile?->instagram_handle) }}"
                                               placeholder="yellowpos_com">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">Content Pillars <span class="text-muted fw-normal">(comma-separated)</span></label>
                                    <input type="text" name="content_pillars" class="form-control"
                                           value="{{ old('content_pillars', $brandProfile?->content_pillars ? implode(', ', $brandProfile->content_pillars) : '') }}"
                                           placeholder="e.g. product features, success stories, tips, promotions">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">Default Hashtags</label>
                                    <textarea name="hashtags" class="form-control" rows="2"
                                              placeholder="#YellowPOS #POS #Lebanon">{{ old('hashtags', $brandProfile?->hashtags) }}</textarea>
                                </div>
                            </div>

                            {{-- Right column — Voice --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">Sample Captions
                                        <span class="text-muted fw-normal">(paste 3–5 of your best posts)</span>
                                    </label>
                                    <textarea name="sample_captions" id="sampleCaptions" class="form-control" rows="8"
                                              placeholder="Paste your existing Instagram/Facebook captions here. The more examples you give, the better the AI learns your voice.">{{ old('sample_captions', $brandProfile?->sample_captions) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <button type="button" id="analyzeVoiceBtn" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-magic me-1"></i> Analyze Voice with AI
                                    </button>
                                    <div id="analyzeSpinner" class="text-center mt-2 d-none">
                                        <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                        <span class="small text-muted ms-2">Analyzing your writing style...</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" style="font-size:.875rem;">
                                        Brand Voice Summary
                                        <span class="text-muted fw-normal">(AI-generated, editable)</span>
                                    </label>
                                    <textarea name="voice_summary" id="voiceSummary" class="form-control" rows="6"
                                              placeholder="Click 'Analyze Voice' to auto-generate, or write your own description of your brand's writing style.">{{ old('voice_summary', $brandProfile?->voice_summary) }}</textarea>
                                    @if($brandProfile?->hasVoice())
                                        <div class="form-text text-success">
                                            <i class="bi bi-check-circle me-1"></i> Voice profile active — all AI generations use this context
                                        </div>
                                    @else
                                        <div class="form-text">Once set, this context is injected into every image and caption the AI generates.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-yellow px-4">Save Brand Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Connections --}}
        <div class="settings-panel {{ $activeTab === 'connections' ? 'active' : '' }}">
            <div class="card">
                <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-plug text-muted"></i> Ad Account Connections
                    </span>
                    <a href="{{ route('meta.connect') }}" class="btn btn-yellow btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Connect Meta Ads
                    </a>
                </div>
                <div class="card-body p-4">
                    @if($accounts->isEmpty())
                        <div class="text-center py-4">
                            <div style="width:56px;height:56px;background:#f3f4f6;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                                <i class="bi bi-plug fs-4 text-muted"></i>
                            </div>
                            <div class="fw-semibold mb-1">No accounts connected</div>
                            <div class="small text-muted mb-3">Connect your Meta Ads account to start monitoring campaigns.</div>
                            <a href="{{ route('meta.connect') }}" class="btn btn-yellow btn-sm px-4">
                                <i class="bi bi-facebook me-1"></i> Connect Meta Ads
                            </a>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($accounts as $account)
                                <div class="account-card">
                                    <div class="account-icon">
                                        <i class="bi bi-facebook"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-semibold" style="font-size:.9rem;">{{ $account->account_name }}</div>
                                        <div class="text-muted" style="font-size:.8rem;">{{ $account->account_id }}</div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="{{ $account->is_active ? 'badge-active' : 'badge-paused' }}">
                                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <div class="text-muted" style="font-size:.8rem;">
                                            Connected {{ $account->created_at->diffForHumans() }}
                                        </div>
                                        <form method="POST" action="{{ route('settings.ad-accounts.remove', $account->id) }}"
                                              onsubmit="return confirm('Remove {{ $account->account_name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Organization --}}
        @if($tenant)
        <div class="settings-panel {{ $activeTab === 'organization' ? 'active' : '' }}">
            <div class="card">
                <div class="card-header py-3 px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-building text-muted"></i> Organization
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
                        <div style="width:52px;height:52px;border-radius:12px;background:#111;display:flex;align-items:center;justify-content:center;color:var(--yellow);font-size:1.3rem;font-weight:700;">
                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:1.1rem;">{{ $tenant->name }}</div>
                            <div class="text-muted small">{{ $tenant->slug }}</div>
                        </div>
                        <div class="ms-auto">
                            <span class="plan-badge plan-{{ $tenant->plan }}">
                                @if($tenant->plan === 'pro') <i class="bi bi-star-fill"></i> @endif
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="stat-mini">
                                <div class="val">{{ $stats['images'] }}</div>
                                <div class="lbl">Images Generated</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini">
                                <div class="val">{{ $stats['campaigns'] }}</div>
                                <div class="lbl">Campaigns Synced</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini">
                                <div class="val">{{ $stats['accounts'] }}</div>
                                <div class="lbl">Ad Accounts</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2" style="font-size:.875rem;">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Status</span>
                            <span class="{{ $tenant->is_active ? 'badge-active' : 'badge-paused' }}">
                                {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Member since</span>
                            <span class="fw-semibold">{{ $tenant->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Plan</span>
                            <span class="fw-semibold">{{ ucfirst($tenant->plan) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('analyzeVoiceBtn')?.addEventListener('click', async function() {
    const captions = document.getElementById('sampleCaptions').value.trim();
    if (captions.length < 50) {
        alert('Please paste at least a few captions before analyzing.');
        return;
    }

    this.disabled = true;
    document.getElementById('analyzeSpinner').classList.remove('d-none');

    try {
        const res = await fetch('{{ route("settings.brand.analyze") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                sample_captions: captions,
                business_name: document.querySelector('[name="business_name"]')?.value || '',
            }),
        });
        const data = await res.json();
        if (data.voice_summary) {
            document.getElementById('voiceSummary').value = data.voice_summary;
        }
    } catch (e) {
        alert('Analysis failed. Please try again.');
    } finally {
        this.disabled = false;
        document.getElementById('analyzeSpinner').classList.add('d-none');
    }
});
</script>
@endpush
