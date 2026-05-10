@extends('layouts.app')

@section('title', 'Media Studio')
@section('page-title', 'Media Studio')

@push('styles')
<style>
/* ── Studio Design System ─────────────────────────── */
.studio-panel {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.studio-panel-header {
    padding: 1rem 1.5rem 0;
    border-bottom: 1px solid #e9ecef;
    flex-shrink: 0;
}
.studio-panel-body {
    padding: 1.25rem 1.5rem 1.5rem;
    overflow-y: auto;
    flex: 1;
}

/* Mode toggle */
.mode-toggle {
    display: inline-flex;
    background: #f9fafb;
    border-radius: 100px;
    padding: 3px;
    gap: 2px;
    margin-bottom: 1.1rem;
}
.mode-toggle button {
    background: transparent;
    border: none;
    color: #6b7280;
    font-size: .775rem;
    font-weight: 600;
    padding: .375rem 1rem;
    border-radius: 100px;
    cursor: pointer;
    transition: all .2s;
    letter-spacing: .01em;
    font-family: inherit;
}
.mode-toggle button.active { background: #F5C300; color: #111; }
.mode-toggle button:hover:not(.active) { color: #374151; }

/* Labels */
.s-label {
    display: block;
    font-size: .67rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: .45rem;
}
.s-optional { color: #9ca3af; font-weight: 500; text-transform: none; letter-spacing: 0; font-size: .68rem; }

/* Inputs */
.s-input {
    width: 100%;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #111827;
    padding: .65rem .875rem;
    font-size: .85rem;
    font-family: inherit;
    transition: border-color .15s, box-shadow .15s;
    outline: none;
    resize: none;
    display: block;
}
.s-input::placeholder { color: #9ca3af; }
.s-input:focus { border-color: #F5C300; box-shadow: 0 0 0 3px rgba(245,195,0,.1); }
.s-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%2352525b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .75rem center;
    padding-right: 2.25rem;
    cursor: pointer;
}
.s-input option { background: #ffffff; color: #111827; }

/* Format selector */
.format-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: .4rem; }
.fmt-opt { position: relative; }
.fmt-opt input { position: absolute; opacity: 0; width: 0; height: 0; }
.fmt-opt label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    gap: .35rem;
    padding: .6rem .25rem .55rem;
    background: #f9fafb;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all .15s;
    text-align: center;
    min-height: 62px;
}
.fmt-opt label:hover { border-color: #9ca3af; }
.fmt-opt input:checked + label { border-color: #F5C300; background: rgba(245,195,0,.07); }
.fmt-thumb { background: #e5e7eb; border-radius: 2px; transition: background .15s; margin: 0 auto; }
.fmt-opt input:checked + label .fmt-thumb { background: #F5C300; }
.fmt-name { font-size: .6rem; font-weight: 700; color: #6b7280; letter-spacing: .02em; line-height: 1.2; }
.fmt-opt input:checked + label .fmt-name { color: #F5C300; }

/* Style pills */
.style-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: .4rem; }
.sty-opt { position: relative; }
.sty-opt input { position: absolute; opacity: 0; width: 0; height: 0; }
.sty-opt label {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: .5rem .25rem;
    background: #f9fafb;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    font-size: .72rem;
    font-weight: 600;
    color: #6b7280;
    transition: all .15s;
    text-align: center;
}
.sty-opt label:hover { border-color: #9ca3af; color: #374151; }
.sty-opt input:checked + label { border-color: #F5C300; background: rgba(245,195,0,.07); color: #F5C300; }

/* Language pills */
.lang-pills { display: flex; gap: .4rem; flex-wrap: wrap; }
.lang-opt { position: relative; }
.lang-opt input { position: absolute; opacity: 0; width: 0; height: 0; }
.lang-opt label {
    padding: .375rem .875rem;
    background: #f9fafb;
    border: 1.5px solid #e5e7eb;
    border-radius: 100px;
    cursor: pointer;
    font-size: .75rem;
    font-weight: 600;
    color: #6b7280;
    transition: all .15s;
    white-space: nowrap;
    display: block;
}
.lang-opt label:hover { border-color: #9ca3af; color: #374151; }
.lang-opt input:checked + label { border-color: #F5C300; background: rgba(245,195,0,.08); color: #F5C300; }

/* Generate button */
.btn-gen {
    width: 100%;
    padding: .85rem;
    background: #F5C300;
    color: #111;
    border: none;
    border-radius: 10px;
    font-size: .875rem;
    font-weight: 700;
    letter-spacing: .02em;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: background .15s, transform .1s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    font-family: inherit;
}
.btn-gen:hover:not(:disabled) { background: #e6b800; transform: translateY(-1px); }
.btn-gen:active { transform: translateY(0); }
.btn-gen:disabled { opacity: .45; cursor: not-allowed; }
.btn-gen::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.3) 50%, transparent 60%);
    transform: translateX(-150%);
    transition: transform .55s;
}
.btn-gen:not(:disabled):hover::after { transform: translateX(150%); }

/* Canvas */
.studio-canvas {
    background: #f3f4f6;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.75rem;
    min-height: 480px;
    position: relative;
}
.canvas-header {
    padding: .75rem 1.25rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

/* Idle state */
.idle-ring {
    width: 72px; height: 72px;
    border-radius: 50%;
    border: 1.5px dashed #e9ecef;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: #d1d5db;
}

/* Loading */
.spin-ring {
    width: 52px; height: 52px;
    border-radius: 50%;
    border: 2.5px solid #e5e7eb;
    border-top-color: #F5C300;
    animation: studioSpin 1s linear infinite;
    margin: 0 auto .875rem;
}
@keyframes studioSpin { to { transform: rotate(360deg); } }
.dot-row { display: flex; gap: .3rem; justify-content: center; margin-top: .625rem; }
.dot-row span {
    width: 4px; height: 4px;
    background: #F5C300;
    border-radius: 50%;
    animation: dotPulse .9s ease-in-out infinite;
    opacity: .25;
}
.dot-row span:nth-child(2) { animation-delay: .15s; }
.dot-row span:nth-child(3) { animation-delay: .3s; }
@keyframes dotPulse {
    0%,80%,100% { opacity: .25; transform: scale(.7); }
    40% { opacity: 1; transform: scale(1); }
}

/* Result image */
#resultImage { border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,.12); }

/* Caption */
.caption-wrap {
    background: #f9fafb;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: .75rem 1rem;
    margin-top: .875rem;
    width: 100%;
}
.caption-meta { display: flex; align-items: center; justify-content: space-between; margin-bottom: .4rem; }
.caption-tag { font-size: .62rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #9ca3af; }
.caption-body { font-size: .8rem; line-height: 1.6; color: #6b7280; white-space: pre-wrap; max-height: 110px; overflow-y: auto; }

/* Action bar */
.canvas-actions { display: flex; gap: .45rem; flex-wrap: wrap; justify-content: center; margin-top: 1rem; }
.btn-act {
    padding: .4rem .9rem;
    border-radius: 8px;
    font-size: .775rem;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    text-decoration: none;
    font-family: inherit;
}
.btn-act-primary { background: #F5C300; color: #111; border-color: #F5C300; }
.btn-act-primary:hover { background: #e6b800; color: #111; }
.btn-act-ghost { background: transparent; color: #6b7280; border-color: #d1d5db; }
.btn-act-ghost:hover { background: #f9fafb; color: #111827; border-color: #9ca3af; }

/* Error state */
.error-icon {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.18);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto .875rem;
    font-size: 1.25rem;
    color: #ef4444;
}

/* Dropzone */
.s-dropzone {
    border: 1.5px dashed #d1d5db;
    border-radius: 8px;
    background: #f9fafb;
    min-height: 120px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: .3rem;
    cursor: pointer;
    transition: border-color .15s, background .15s;
}
.s-dropzone:hover, .s-dropzone.drag-over { border-color: #F5C300; background: rgba(245,195,0,.04); }

/* Ideas section */
.ideas-panel {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 14px;
    overflow: hidden;
    margin-top: .875rem;
}
.ideas-head {
    padding: .875rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.idea-card {
    background: #f9fafb;
    border: 1.5px solid #e9ecef;
    border-radius: 10px;
    padding: .875rem 1rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: border-color .2s;
    cursor: default;
}
.idea-card:hover { border-color: #9ca3af; }
.idea-badge {
    display: inline-block;
    font-size: .6rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
    padding: .18em .55em; border-radius: 100px;
    background: rgba(245,195,0,.12); color: #F5C300;
    margin-bottom: .45rem;
}
.idea-fmt {
    font-size: .65rem; color: #9ca3af; font-weight: 600;
    display: inline-flex; align-items: center; gap: .2rem;
    margin-left: .4rem;
}
.idea-text { font-size: .8rem; line-height: 1.55; color: #374151; flex: 1; margin-bottom: .65rem; }

/* Queue items */
.queue-item {
    display: flex; align-items: center; gap: .5rem;
    background: #f9fafb; border: 1px solid #e9ecef;
    border-radius: 7px; padding: .45rem .65rem;
}
.qi-info { flex: 1; min-width: 0; }
.qi-topic { font-size: .775rem; font-weight: 600; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.qi-meta { font-size: .65rem; color: #9ca3af; margin-top: .1rem; }
.qi-remove { background: none; border: none; color: #c4c4c4; cursor: pointer; padding: .2rem .35rem; border-radius: 4px; line-height: 1; flex-shrink: 0; font-size: .85rem; }
.qi-remove:hover { background: #fee2e2; color: #ef4444; }
.btn-gen-outline {
    width: 100%; padding: .75rem; background: transparent;
    color: #374151; border: 1.5px solid #d1d5db; border-radius: 10px;
    font-size: .875rem; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    font-family: inherit; transition: all .15s;
}
.btn-gen-outline:hover { border-color: #9ca3af; background: #f9fafb; }

/* Image result grid */
.img-result-grid { display: grid; gap: .625rem; width: 100%; }
.img-result-grid[data-count="1"] { grid-template-columns: 1fr; max-width: 380px; margin: 0 auto; }
.img-result-grid[data-count="2"],
.img-result-grid[data-count="3"],
.img-result-grid[data-count="4"] { grid-template-columns: 1fr 1fr; }

.img-slot {
    background: #f3f4f6;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.img-slot img { width: 100%; display: block; object-fit: contain; }
.slot-actions {
    position: absolute;
    bottom: .5rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: .3rem;
    opacity: 0;
    transition: opacity .18s;
    white-space: nowrap;
}
.img-slot:hover .slot-actions { opacity: 1; }

/* Scrollbar */
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
</style>
@endpush

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
@php $ep = $editImage?->generation_params ?? []; @endphp

<div class="row g-3" style="min-height:560px;">

    {{-- ── Left: Controls ───────────────────── --}}
    <div class="col-xl-5 d-flex flex-column">
        <div class="studio-panel flex-grow-1">
            <div class="studio-panel-header">
                <div class="mode-toggle">
                    <button id="tab-generate" type="button" onclick="switchTab('generate')"
                            class="{{ request('mode') !== 'edit' ? 'active' : '' }}">
                        <i class="bi bi-magic me-1"></i> Generate
                    </button>
                    <button id="tab-edit" type="button" onclick="switchTab('edit')"
                            class="{{ request('mode') === 'edit' ? 'active' : '' }}">
                        <i class="bi bi-pencil-square me-1"></i> Edit Image
                    </button>
                </div>
            </div>

            <div class="studio-panel-body">

                @if($editImage)
                <div style="background:rgba(245,195,0,.08);border:1px solid rgba(245,195,0,.2);border-radius:8px;padding:.55rem .875rem;font-size:.775rem;color:#F5C300;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
                    <i class="bi bi-pencil-square" style="flex-shrink:0;"></i>
                    Editing #{{ $editImage->id }} — tweak and regenerate.
                </div>
                @endif

                {{-- ── Generate Form ── --}}
                <form id="generateForm" class="{{ request('mode') === 'edit' ? 'd-none' : '' }}">
                    @csrf

                    <div style="margin-bottom:.875rem;">
                        <label class="s-label">Topic / Description <span style="color:#dc2626;">*</span></label>
                        <textarea name="topic" id="topic" class="s-input" rows="3"
                                  placeholder="e.g. Ramadan promo for a coffee shop — 30% off all drinks">{{ $editImage?->generation_params['topic'] ?? '' }}</textarea>
                    </div>

                    <div style="margin-bottom:.875rem;">
                        <label class="s-label">Format</label>
                        <div class="format-grid">
                            <div class="fmt-opt">
                                <input type="radio" name="format" id="fmt-post" value="post" @checked(($ep['format'] ?? 'post') === 'post')>
                                <label for="fmt-post">
                                    <div class="fmt-thumb" style="width:20px;height:25px;"></div>
                                    <span class="fmt-name">Post<br>4:5</span>
                                </label>
                            </div>
                            <div class="fmt-opt">
                                <input type="radio" name="format" id="fmt-square" value="square" @checked(($ep['format'] ?? '') === 'square')>
                                <label for="fmt-square">
                                    <div class="fmt-thumb" style="width:22px;height:22px;"></div>
                                    <span class="fmt-name">Square<br>1:1</span>
                                </label>
                            </div>
                            <div class="fmt-opt">
                                <input type="radio" name="format" id="fmt-story" value="story" @checked(($ep['format'] ?? '') === 'story')>
                                <label for="fmt-story">
                                    <div class="fmt-thumb" style="width:13px;height:24px;"></div>
                                    <span class="fmt-name">Story<br>9:16</span>
                                </label>
                            </div>
                            <div class="fmt-opt">
                                <input type="radio" name="format" id="fmt-banner" value="banner" @checked(($ep['format'] ?? '') === 'banner')>
                                <label for="fmt-banner">
                                    <div class="fmt-thumb" style="width:28px;height:16px;"></div>
                                    <span class="fmt-name">Banner<br>16:9</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom:.875rem;">
                        <label class="s-label">Style</label>
                        <div class="style-grid">
                            <div class="sty-opt">
                                <input type="radio" name="style" id="sty-realistic" value="realistic" @checked(($ep['style'] ?? 'realistic') === 'realistic')>
                                <label for="sty-realistic">Realistic</label>
                            </div>
                            <div class="sty-opt">
                                <input type="radio" name="style" id="sty-illustrated" value="illustrated" @checked(($ep['style'] ?? '') === 'illustrated')>
                                <label for="sty-illustrated">Illustrated</label>
                            </div>
                            <div class="sty-opt">
                                <input type="radio" name="style" id="sty-minimalist" value="minimalist" @checked(($ep['style'] ?? '') === 'minimalist')>
                                <label for="sty-minimalist">Minimalist</label>
                            </div>
                            <div class="sty-opt">
                                <input type="radio" name="style" id="sty-bold" value="bold" @checked(($ep['style'] ?? '') === 'bold')>
                                <label for="sty-bold">Bold</label>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom:.875rem;">
                        <label class="s-label">Color Hint <span class="s-optional">(optional)</span></label>
                        <input type="text" name="color_hint" class="s-input"
                               placeholder="e.g. Yellow and black, or #F5C300"
                               value="{{ $ep['color_hint'] ?? '' }}">
                    </div>

                    <div style="margin-bottom:.875rem;">
                        <label class="s-label">Text Overlay <span class="s-optional">(optional)</span></label>
                        <input type="text" name="text_overlay" class="s-input"
                               placeholder="e.g. خصم 30% — Summer Sale"
                               value="{{ $ep['text_overlay'] ?? '' }}">
                    </div>

                    <div style="margin-bottom:.875rem;">
                        <label class="s-label">Language</label>
                        <div class="lang-pills">
                            <div class="lang-opt">
                                <input type="radio" name="language" id="lang-en" value="English" @checked(($ep['language'] ?? '') === 'English')>
                                <label for="lang-en">English</label>
                            </div>
                            <div class="lang-opt">
                                <input type="radio" name="language" id="lang-ar" value="Arabic" @checked(($ep['language'] ?? '') === 'Arabic')>
                                <label for="lang-ar">Arabic</label>
                            </div>
                            <div class="lang-opt">
                                <input type="radio" name="language" id="lang-both" value="English and Arabic" @checked(($ep['language'] ?? 'English and Arabic') === 'English and Arabic')>
                                <label for="lang-both">EN + AR</label>
                            </div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:.75rem;">
                        <button type="button" id="addToQueueBtn" class="btn-gen-outline">
                            <i class="bi bi-plus-lg"></i> Add to Queue
                        </button>
                        <button type="button" id="generateBtn" class="btn-gen">
                            <i class="bi bi-magic"></i> Generate Now
                        </button>
                    </div>

                    {{-- Queue section --}}
                    <div id="queueSection" class="d-none" style="border-top:1px solid #e9ecef;padding-top:.875rem;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                            <span class="s-label" style="margin:0;">Queue (<span id="queueCount">0</span>)</span>
                            <button type="button" onclick="clearQueue()"
                                    style="background:none;border:none;font-size:.72rem;color:#9ca3af;cursor:pointer;padding:0;font-family:inherit;">
                                Clear all
                            </button>
                        </div>
                        <div id="queueList" style="display:flex;flex-direction:column;gap:.35rem;margin-bottom:.75rem;"></div>
                        <button type="button" id="runQueueBtn" class="btn-gen">
                            <i class="bi bi-play-fill"></i> Generate Queue (<span id="queueCountBtn">0</span> jobs)
                        </button>
                    </div>
                </form>

                {{-- ── Edit Form ── --}}
                <form id="editForm" class="{{ request('mode') !== 'edit' ? 'd-none' : '' }}">
                    @csrf

                    <div style="margin-bottom:.875rem;">
                        <label class="s-label">Upload Image <span style="color:#dc2626;">*</span></label>
                        <div id="editDropzone" class="s-dropzone"
                             onclick="document.getElementById('editFileInput').click()"
                             ondragover="event.preventDefault();this.classList.add('drag-over');"
                             ondragleave="this.classList.remove('drag-over');"
                             ondrop="handleEditDrop(event)">
                            <i class="bi bi-cloud-upload" style="font-size:1.4rem;color:#9ca3af;"></i>
                            <div style="font-size:.8rem;color:#6b7280;">Click to upload or drag & drop</div>
                            <div style="font-size:.7rem;color:#9ca3af;">JPG, PNG, WEBP — max 10MB</div>
                        </div>
                        <input type="file" id="editFileInput" name="image" accept="image/*" class="d-none">
                        <div id="editPreviewWrap" class="d-none mt-2" style="position:relative;display:inline-block;">
                            <img id="editPreviewImg" src="" alt="" style="max-height:140px;border-radius:8px;display:block;">
                            <button type="button" onclick="clearEditImage()"
                                    style="position:absolute;top:.3rem;right:.3rem;background:#fff;border:1px solid #d1d5db;color:#6b7280;border-radius:6px;padding:.15rem .4rem;font-size:.7rem;cursor:pointer;line-height:1;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <input type="hidden" id="editImageId" name="image_id">
                    </div>

                    <div style="margin-bottom:.875rem;">
                        <label class="s-label">What to change? <span style="color:#dc2626;">*</span></label>
                        <textarea name="edit_prompt" id="editPrompt" class="s-input" rows="4"
                                  placeholder="e.g. Change the background to yellow, make the headline bigger, add a burger on the right..."></textarea>
                    </div>

                    <div style="margin-bottom:1.25rem;">
                        <label class="s-label">Format</label>
                        <select name="format" class="s-input s-select">
                            <option value="post">Post (4:5)</option>
                            <option value="square">Square (1:1)</option>
                            <option value="story">Story (9:16)</option>
                            <option value="banner">Banner (16:9)</option>
                        </select>
                    </div>

                    <button type="submit" id="editBtn" class="btn-gen">
                        <i class="bi bi-pencil-square"></i> Apply Edits
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- ── Right: Canvas ─────────────────────── --}}
    <div class="col-xl-7 d-flex flex-column">
        <div class="studio-panel flex-grow-1">
            <div class="canvas-header">
                <span style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;">Preview Canvas</span>
                <span id="canvasStatus" style="font-size:.7rem;color:#6b7280;display:flex;align-items:center;gap:.35rem;">
                    <span id="statusDot" style="width:6px;height:6px;border-radius:50%;background:#d1d5db;display:inline-block;"></span>
                    Ready
                </span>
            </div>

            <div class="studio-canvas" id="canvasArea">

                {{-- Idle --}}
                <div id="idleState">
                    <div class="idle-ring"><i class="bi bi-magic"></i></div>
                    <div style="font-size:.85rem;font-weight:600;color:#6b7280;margin-bottom:.25rem;text-align:center;">Canvas is empty</div>
                    <div style="font-size:.75rem;color:#9ca3af;text-align:center;">Configure your image and hit Generate</div>
                </div>

                {{-- Loading --}}
                <div id="loadingState" class="d-none" style="text-align:center;">
                    <div class="spin-ring"></div>
                    <div style="font-size:.875rem;font-weight:600;color:#374151;">Generating your image…</div>
                    <div style="font-size:.75rem;color:#9ca3af;margin-top:.2rem;">This may take 20–60 seconds</div>
                    <div class="dot-row"><span></span><span></span><span></span></div>
                </div>

                {{-- Result --}}
                <div id="resultState" class="d-none w-100">
                    <div id="imgResultGrid" class="img-result-grid" data-count="1"></div>

                    <div class="canvas-actions" style="margin-top:.875rem;">
                        <button onclick="resetCanvas()" class="btn-act btn-act-ghost">
                            <i class="bi bi-arrow-repeat"></i> New
                        </button>
                        <a href="{{ route('media.library') }}" class="btn-act btn-act-ghost">
                            <i class="bi bi-images"></i> Library
                        </a>
                    </div>
                </div>

                {{-- Error --}}
                <div id="errorState" class="d-none" style="text-align:center;">
                    <div class="error-icon"><i class="bi bi-exclamation-triangle"></i></div>
                    <div style="font-size:.875rem;font-weight:600;color:#ef4444;margin-bottom:.25rem;">Generation failed</div>
                    <div style="font-size:.75rem;color:#9ca3af;margin-bottom:1rem;">Try rephrasing your prompt or removing special characters</div>
                    <button onclick="resetForm()" class="btn-act btn-act-ghost">
                        <i class="bi bi-arrow-repeat"></i> Try Again
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ── Prompt Ideas ─────────────────────────────── --}}
<div class="ideas-panel">
    <div class="ideas-head">
        <div>
            <span style="font-size:.85rem;font-weight:700;color:#111827;">
                <i class="bi bi-lightbulb me-2" style="color:#F5C300;"></i>Post Prompt Ideas
            </span>
            <span style="font-size:.75rem;color:#6b7280;margin-left:.5rem;">AI ideas based on your brand profile</span>
        </div>
        @if(!$brandProfile?->hasVoice())
            <a href="{{ route('settings.index', ['tab' => 'brand']) }}"
               style="padding:.375rem .875rem;background:rgba(245,195,0,.08);border:1px solid rgba(245,195,0,.2);border-radius:8px;color:#F5C300;font-size:.75rem;font-weight:600;text-decoration:none;">
                <i class="bi bi-palette me-1"></i> Set up Brand Profile
            </a>
        @endif
    </div>

    <div style="padding:1.1rem 1.5rem;">
        @if($brandProfile?->hasVoice())
        <div style="display:flex;gap:.625rem;margin-bottom:1.1rem;">
            <div style="position:relative;flex:1;">
                <i class="bi bi-search" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.8rem;pointer-events:none;"></i>
                <input type="text" id="ideasTopic" class="s-input" style="padding-left:2.1rem;"
                       placeholder="Optional: type a topic — Ramadan, summer sale, new feature…">
            </div>
            <button id="getIdeasBtn" class="btn-gen" style="width:auto;padding:.65rem 1.25rem;font-size:.8rem;">
                <i class="bi bi-lightbulb"></i> Get Ideas
            </button>
        </div>
        @endif

        <div id="ideasIdle" style="text-align:center;padding:1.75rem 0;color:#9ca3af;">
            <i class="bi bi-lightbulb" style="font-size:1.5rem;display:block;margin-bottom:.4rem;"></i>
            <div style="font-size:.775rem;">Describe a topic or click <strong style="color:#6b7280;">Get Ideas</strong> for brand-based suggestions</div>
        </div>
        <div id="ideasLoading" class="d-none" style="text-align:center;padding:1.75rem 0;">
            <div class="spin-ring" style="width:32px;height:32px;border-width:2px;"></div>
            <div style="font-size:.775rem;color:#6b7280;margin-top:.4rem;">Thinking up ideas…</div>
        </div>
        <div id="ideasGrid" class="row g-3 d-none"></div>
        <div id="ideasError" class="d-none"
             style="background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.15);border-radius:8px;padding:.65rem 1rem;color:#f87171;font-size:.8rem;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let pollingInterval = null;
let activeImages    = []; // [{ id, status }]

/* ── Tab switching ─────────────────────────── */
function switchTab(tab) {
    const isEdit = tab === 'edit';
    document.getElementById('generateForm').classList.toggle('d-none', isEdit);
    document.getElementById('editForm').classList.toggle('d-none', !isEdit);
    document.getElementById('tab-generate').classList.toggle('active', !isEdit);
    document.getElementById('tab-edit').classList.toggle('active', isEdit);
    showState('idle');
}

/* ── File upload ───────────────────────────── */
document.getElementById('editFileInput').addEventListener('change', function () {
    if (this.files[0]) previewEditFile(this.files[0]);
});
function handleEditDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        document.getElementById('editFileInput').files = e.dataTransfer.files;
        previewEditFile(file);
    }
}
function previewEditFile(file) {
    const reader = new FileReader();
    reader.onload = ev => {
        document.getElementById('editPreviewImg').src = ev.target.result;
        document.getElementById('editDropzone').classList.add('d-none');
        document.getElementById('editPreviewWrap').classList.remove('d-none');
        document.getElementById('editImageId').value = '';
    };
    reader.readAsDataURL(file);
}
function clearEditImage() {
    document.getElementById('editFileInput').value = '';
    document.getElementById('editImageId').value = '';
    document.getElementById('editDropzone').classList.remove('d-none');
    document.getElementById('editPreviewWrap').classList.add('d-none');
}

/* ── Edit form ─────────────────────────────── */
document.getElementById('editForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const prompt  = document.getElementById('editPrompt').value.trim();
    const imageId = document.getElementById('editImageId').value;
    const fileInp = document.getElementById('editFileInput');
    if (!prompt) { alert('Please describe what to change.'); return; }
    if (!imageId && (!fileInp.files || !fileInp.files[0])) {
        alert('Please upload an image to edit.'); return;
    }
    showState('loading');
    document.getElementById('editBtn').disabled = true;
    try {
        const res  = await fetch('{{ route("media.edit") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: new FormData(this),
        });
        const data = await res.json();
        if (data.error) { showState('error'); return; }
        if (data.id) { buildGrid([data.id]); showState('result'); startMultiPolling(); }
        else showState('error');
    } catch { showState('error'); }
});

/* ── Queue ─────────────────────────────────── */
let queue = [];

function getFormSnapshot() {
    const fd = new FormData(document.getElementById('generateForm'));
    return {
        _qid:         Date.now() + Math.random(),
        topic:        fd.get('topic')?.trim() ?? '',
        format:       fd.get('format') ?? 'post',
        style:        fd.get('style') ?? 'realistic',
        color_hint:   fd.get('color_hint') ?? '',
        text_overlay: fd.get('text_overlay') ?? '',
        language:     fd.get('language') ?? 'English and Arabic',
    };
}

document.getElementById('addToQueueBtn').addEventListener('click', function () {
    const snap = getFormSnapshot();
    if (!snap.topic) { alert('Please enter a topic first.'); return; }
    queue.push(snap);
    renderQueue();
});

document.getElementById('runQueueBtn').addEventListener('click', async function () {
    if (!queue.length) return;
    const items  = [...queue];
    queue = [];
    renderQueue();
    await dispatchItems(items);
});

document.getElementById('generateBtn').addEventListener('click', async function () {
    const snap = getFormSnapshot();
    if (!snap.topic) { alert('Please enter a topic.'); return; }
    await dispatchItems([snap]);
});

function renderQueue() {
    const section = document.getElementById('queueSection');
    const list    = document.getElementById('queueList');
    document.getElementById('queueCount').textContent    = queue.length;
    document.getElementById('queueCountBtn').textContent = queue.length;
    if (!queue.length) { section.classList.add('d-none'); return; }
    section.classList.remove('d-none');
    list.innerHTML = queue.map((item, i) => `
        <div class="queue-item" id="qi-${item._qid}">
            <div class="qi-info">
                <div class="qi-topic">${escHtml(item.topic)}</div>
                <div class="qi-meta">${item.format} · ${item.style} · ${item.language}</div>
            </div>
            <button class="qi-remove" onclick="removeFromQueue(${i})" title="Remove">
                <i class="bi bi-x"></i>
            </button>
        </div>`).join('');
}

function removeFromQueue(index) {
    queue.splice(index, 1);
    renderQueue();
}

function clearQueue() {
    queue = [];
    renderQueue();
}

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function dispatchItems(items) {
    showState('loading');
    document.getElementById('generateBtn').disabled = true;
    document.getElementById('runQueueBtn').disabled = true;

    const csrf   = document.querySelector('meta[name="csrf-token"]').content;
    const allIds = [];

    await Promise.all(items.map(async item => {
        try {
            const body = new FormData();
            ['topic','format','style','color_hint','text_overlay','language'].forEach(k => body.append(k, item[k]));
            body.append('count', 1);
            const res  = await fetch('{{ route("media.generate") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf },
                body,
            });
            const data = await res.json();
            if (data.ids?.length) allIds.push(...data.ids);
        } catch {}
    }));

    document.getElementById('generateBtn').disabled = false;
    document.getElementById('runQueueBtn').disabled = false;

    if (allIds.length) { buildGrid(allIds); showState('result'); startMultiPolling(); }
    else showState('error');
}

/* ── Grid builder ──────────────────────────── */
function buildGrid(ids) {
    if (pollingInterval) clearInterval(pollingInterval);
    activeImages = ids.map(id => ({ id, status: 'pending' }));
    const grid = document.getElementById('imgResultGrid');
    grid.setAttribute('data-count', ids.length);
    grid.innerHTML = ids.map(id => `
        <div class="img-slot" id="img-slot-${id}">
            <div style="text-align:center;padding:1.5rem 0;">
                <div class="spin-ring" style="width:28px;height:28px;border-width:2px;margin:0 auto .4rem;"></div>
                <div style="font-size:.7rem;color:#9ca3af;">Generating…</div>
            </div>
        </div>`).join('');
    setStatus('#F5C300', ids.length === 1 ? 'Generating…' : `Generating ${ids.length} images…`, true);
}

/* ── Multi-image polling ───────────────────── */
function startMultiPolling() {
    pollingInterval = setInterval(async () => {
        const pending = activeImages.filter(img => img.status === 'pending');
        if (!pending.length) { clearInterval(pollingInterval); return; }

        await Promise.all(pending.map(async img => {
            try {
                const res  = await fetch(`{{ url('/media-studio/status') }}/${img.id}`);
                const data = await res.json();
                if (data.status === 'done' && data.url) {
                    img.status = 'done';
                    fillSlot(img.id, data.url);
                } else if (data.status === 'failed') {
                    img.status = 'failed';
                    failSlot(img.id);
                }
            } catch {}
        }));

        const stillPending = activeImages.filter(img => img.status === 'pending').length;
        if (!stillPending) {
            clearInterval(pollingInterval);
            const allFailed = activeImages.every(img => img.status === 'failed');
            setStatus(allFailed ? '#ef4444' : '#22c55e', allFailed ? 'Failed' : 'Done', false);
        } else {
            setStatus('#F5C300', `${stillPending} remaining…`, true);
        }
    }, 3000);
}

function fillSlot(id, url) {
    const slot = document.getElementById('img-slot-' + id);
    if (!slot) return;
    slot.innerHTML = `
        <img src="${url}" alt="">
        <div class="slot-actions">
            <a href="${url}" download class="btn-act btn-act-primary" style="font-size:.7rem;padding:.3rem .65rem;">
                <i class="bi bi-download"></i> Save
            </a>
            <button class="btn-act btn-act-ghost" style="font-size:.7rem;padding:.3rem .65rem;"
                    onclick="sendToEdit('${url}',${id})">
                <i class="bi bi-pencil"></i> Edit
            </button>
        </div>`;
}

function failSlot(id) {
    const slot = document.getElementById('img-slot-' + id);
    if (!slot) return;
    slot.innerHTML = `
        <div style="text-align:center;padding:1.5rem 0;">
            <div class="error-icon" style="width:36px;height:36px;font-size:1rem;margin:0 auto .4rem;">
                <i class="bi bi-x"></i>
            </div>
            <div style="font-size:.7rem;color:#9ca3af;">Failed</div>
        </div>`;
}

function sendToEdit(url, id) {
    switchTab('edit');
    document.getElementById('editImageId').value = id;
    document.getElementById('editPreviewImg').src = url;
    document.getElementById('editDropzone').classList.add('d-none');
    document.getElementById('editPreviewWrap').classList.remove('d-none');
    document.querySelector('.studio-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ── State machine ─────────────────────────── */
function showState(state) {
    ['idle','loading','result','error'].forEach(s =>
        document.getElementById(s + 'State').classList.toggle('d-none', s !== state)
    );
    const map = { idle: ['#d1d5db','Ready',false], loading: ['#F5C300','Generating…',true], error: ['#ef4444','Failed',false] };
    if (map[state]) setStatus(...map[state]);
    if (state !== 'loading') document.getElementById('editBtn').disabled = false;
}

function setStatus(color, label, pulse) {
    const dot    = document.getElementById('statusDot');
    const status = document.getElementById('canvasStatus');
    dot.style.background = color;
    dot.style.animation  = pulse ? 'dotPulse .9s ease-in-out infinite' : 'none';
    status.childNodes[status.childNodes.length - 1].textContent = ' ' + label;
}

function resetCanvas() {
    if (pollingInterval) clearInterval(pollingInterval);
    activeImages = [];
    document.getElementById('imgResultGrid').innerHTML = '';
    document.getElementById('generateForm').reset();
    document.getElementById('fmt-post').checked      = true;
    document.getElementById('sty-realistic').checked = true;
    document.getElementById('lang-both').checked     = true;
    showState('idle');
}

/* ── Prompt Ideas ──────────────────────────── */
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
        this.disabled = true;

        const topic = document.getElementById('ideasTopic')?.value.trim() ?? '';
        const body  = new FormData();
        if (topic) body.append('topic', topic);

        try {
            const res  = await fetch('{{ route("media.prompt-ideas") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body,
            });
            const data = await res.json();
            document.getElementById('ideasLoading').classList.add('d-none');

            if (!res.ok || data.error) {
                const el = document.getElementById('ideasError');
                el.textContent = data.error ?? 'Something went wrong. Please try again.';
                el.classList.remove('d-none');
            } else {
                const grid = document.getElementById('ideasGrid');
                grid.innerHTML = '';
                const formatIcon = { post: 'bi-image', story: 'bi-phone', banner: 'bi-display', square: 'bi-square' };

                (data.prompts ?? []).forEach(item => {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-xl-4';
                    col.innerHTML = `
                        <div class="idea-card">
                            <div style="margin-bottom:.4rem;">
                                <span class="idea-badge">${item.pillar ?? ''}</span>
                                <span class="idea-fmt"><i class="bi ${formatIcon[item.format] ?? 'bi-image'}"></i> ${item.format ?? 'post'}</span>
                            </div>
                            <p class="idea-text">${item.prompt}</p>
                            <button class="btn-gen use-prompt-btn" style="font-size:.75rem;padding:.45rem .75rem;"
                                    data-prompt="${item.prompt.replace(/"/g, '&quot;')}" data-format="${item.format ?? 'post'}">
                                <i class="bi bi-arrow-up-left"></i> Use prompt
                            </button>
                        </div>`;
                    grid.appendChild(col);
                });
                grid.classList.remove('d-none');
            }
        } catch {
            document.getElementById('ideasLoading').classList.add('d-none');
            const el = document.getElementById('ideasError');
            el.textContent = 'Failed to get ideas. Please try again.';
            el.classList.remove('d-none');
        } finally { this.disabled = false; }
    });

    document.getElementById('ideasGrid').addEventListener('click', function (e) {
        const btn = e.target.closest('.use-prompt-btn');
        if (!btn) return;
        document.getElementById('topic').value = btn.dataset.prompt;
        // set format radio
        const fmtId = 'fmt-' + (btn.dataset.format || 'post');
        const fmtEl = document.getElementById(fmtId);
        if (fmtEl) fmtEl.checked = true;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
</script>
@endpush
