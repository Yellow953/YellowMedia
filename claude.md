# CLAUDE.md — YellowMedia

## Project Overview

**YellowMedia** is a SaaS web application under the Yellow brand suite. It helps businesses manage their social media presence through AI-powered image generation and Meta Ads monitoring with AI-driven optimization suggestions.

Initially used internally, YellowMedia is built multi-tenant from day one to support future customer onboarding.

---

## Tech Stack

| Layer | Choice |
|---|---|
| Framework | Laravel 11 (full-stack) |
| Frontend | Pure Blade (no Livewire, no Inertia, no Alpine.js) |
| CSS | Tailwind CSS |
| Database | MySQL 8 |
| Auth | Laravel UI (Bootstrap scaffolding, session-based) |
| AI Image Generation | Gemini 3 Pro Image (Nano Banana Pro) via Google AI REST API |
| AI Text / Analysis | Gemini 3.1 Pro via Google AI REST API |
| Meta Ads | Meta Marketing API v19+ |
| File Storage | Laravel Storage (local or S3-compatible) |
| Queue | Laravel Queues + database driver (or Redis if available) |
| Hosting | Same server/environment as YellowPOS |

---

## Brand & UI Guidelines

- **Primary color:** Yellow (`#F5C300`)
- **Secondary:** Black (`#111111`)
- **Background:** White (`#FFFFFF`)
- **Font:** Inter (Google Fonts)
- **Style:** Clean, professional, SaaS dashboard aesthetic
- Consistent with YellowPOS UI patterns (sidebar nav, top bar, card-based layout)

---

## Multi-Tenancy Model

- Each **tenant = one organization/business**
- Users belong to a tenant via `tenant_id` on the `users` table
- All resources (images, ads accounts, campaigns) are scoped by `tenant_id`
- Super admin role for internal (Joe) full access across tenants
- Tenant admin role for customer account owners
- Standard user role for team members within a tenant

---

## Database Schema

### `tenants`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | varchar | Business name |
| slug | varchar | Unique identifier |
| plan | enum | `free`, `starter`, `pro` |
| is_active | boolean | |
| created_at / updated_at | timestamps | |

### `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| tenant_id | bigint FK | |
| name | varchar | |
| email | varchar | Unique |
| password | hashed | |
| role | enum | `super_admin`, `tenant_admin`, `user` |
| created_at / updated_at | timestamps | |

### `generated_images`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| tenant_id | bigint FK | |
| user_id | bigint FK | |
| prompt | text | User input |
| revised_prompt | text | Gemini revised prompt |
| format | enum | `post`, `story`, `banner` |
| size | varchar | e.g. `1024x1024` |
| file_path | varchar | Stored image path |
| google_generation_id | varchar | Google API generation reference |
| status | enum | `pending`, `done`, `failed` |
| created_at / updated_at | timestamps | |

### `meta_ad_accounts`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| tenant_id | bigint FK | |
| account_id | varchar | Meta Ad Account ID |
| account_name | varchar | |
| access_token | text | Encrypted |
| token_expires_at | timestamp | |
| is_active | boolean | |
| created_at / updated_at | timestamps | |

### `meta_campaigns`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| tenant_id | bigint FK | |
| ad_account_id | bigint FK | |
| campaign_id | varchar | Meta Campaign ID |
| name | varchar | |
| status | varchar | ACTIVE / PAUSED / etc. |
| objective | varchar | |
| budget | decimal | Daily or lifetime |
| spend | decimal | Synced from Meta |
| impressions | bigint | |
| clicks | bigint | |
| ctr | decimal | |
| cpc | decimal | |
| roas | decimal | |
| last_synced_at | timestamp | |
| created_at / updated_at | timestamps | |

### `ai_suggestions`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| tenant_id | bigint FK | |
| campaign_id | bigint FK | |
| suggestion | text | AI-generated recommendation |
| type | enum | `budget`, `audience`, `creative`, `copy`, `general` |
| priority | enum | `low`, `medium`, `high` |
| is_dismissed | boolean | |
| created_at / updated_at | timestamps | |

---

## Feature Modules

---

### Module 1 — AI Image Generation

**Goal:** Generate social media images using Gemini 3 Pro Image (Nano Banana Pro) based on user input.

#### User Flow
1. User navigates to **Media Studio**
2. Fills in:
   - Post topic / description (free text)
   - Format: Post (1:1), Story (9:16), Banner (16:9)
   - Style: Realistic / Illustrated / Minimalist / Bold
   - Optional: brand color hint, text overlay (Gemini handles Arabic text well)
3. Clicks **Generate**
4. System builds a structured prompt and calls Gemini 3 Pro Image via queue
5. Image is returned, stored in Laravel Storage, and displayed
6. User can:
   - Download the image
   - Regenerate with tweaks
   - Save to their Media Library
   - Use it directly in Campaign Builder

#### Prompt Engineering
Build the prompt server-side, not from raw user input:

```php
$prompt = "Create a {$style} social media {$format} image about: {$topic}.
Use a professional look suitable for a business.
Color palette: {$colorHint}.
Text overlay (if requested): {$textOverlay}.
Language for any text: {$language}.
High quality, eye-catching, modern marketing design.
Optimized for social media engagement.";
```

#### Image Sizes by Format
| Format | Aspect Ratio | Notes |
|---|---|---|
| Post (1:1) | Square | Facebook / Instagram feed |
| Story (9:16) | Portrait | Instagram / Facebook stories |
| Banner (16:9) | Landscape | Facebook cover / ads |

#### Gemini 3 Pro Image Integration (REST API)
No official PHP SDK — use Laravel HTTP client (Guzzle):

```php
// app/Services/GeminiImageService.php

$response = Http::withHeaders([
    'Content-Type' => 'application/json',
])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3-pro-image-preview:generateContent?key=" . config('services.google.api_key'), [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'responseModalities' => ['IMAGE', 'TEXT'],
    ],
]);

$parts = $response->json('candidates.0.content.parts');
foreach ($parts as $part) {
    if (isset($part['inlineData'])) {
        $imageData = base64_decode($part['inlineData']['data']);
        $mimeType  = $part['inlineData']['mimeType']; // image/png
        // Save to storage
    }
}
```

#### Queue Job: `GenerateImageJob`
- Dispatched on form submit
- Calls Gemini 3 Pro Image REST API
- Decodes base64 image and stores in `storage/app/public/generated/{tenant_id}/`
- Updates `generated_images` record with status and file path
- Vanilla JS polling on frontend checks `/media/status/{id}` every 3 seconds

---

### Module 2 — Meta Ads Monitor

**Goal:** Connect Meta Ads accounts, display campaign performance, and surface AI-generated optimization suggestions.

#### Meta OAuth Connection Flow
1. User goes to **Settings → Ad Accounts**
2. Clicks **Connect Meta Ads**
3. Redirected to Meta OAuth (permissions: `ads_read`, `ads_management`, `business_management`)
4. On callback, store `access_token` (encrypted) in `meta_ad_accounts`
5. Trigger initial sync job

#### Meta API Integration
Use the **Meta Marketing API v19+** via Laravel HTTP client.

```php
// Example: Fetch campaigns
$response = Http::get("https://graph.facebook.com/v19.0/act_{$accountId}/campaigns", [
    'fields'       => 'id,name,status,objective,daily_budget,lifetime_budget',
    'access_token' => $decryptedToken,
]);
```

#### Metrics to Sync (per campaign)
- Spend, Impressions, Clicks, CTR, CPC, CPM, ROAS
- Synced via scheduled job every 6 hours: `SyncMetaCampaignsJob`

#### Ads Dashboard UI
- Summary cards: Total Spend, Total Impressions, Avg CTR, Avg ROAS
- Campaign table with status badges and metrics
- Filter by date range, status, objective
- Per-campaign detail page with full metrics breakdown

#### AI Suggestions Engine
After each sync, run `GenerateAdSuggestionsJob` per campaign using **Gemini 3.1 Pro**:

```php
// app/Services/GeminiTextService.php

$context = "Campaign: {$campaign->name}
Status: {$campaign->status}
Spend: \${$campaign->spend}
CTR: {$campaign->ctr}%
CPC: \${$campaign->cpc}
ROAS: {$campaign->roas}
Objective: {$campaign->objective}
Days running: {$daysRunning}
Frequency: {$campaign->frequency}";

$response = Http::withHeaders([
    'Content-Type' => 'application/json',
])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-pro-preview:generateContent?key=" . config('services.google.api_key'), [
    'contents' => [
        [
            'parts' => [
                ['text' => "You are a Meta Ads expert. Analyze this campaign data and return ONLY a valid JSON array 
                with 2-3 objects: [{\"type\": \"budget|audience|creative|copy|general\", \"priority\": \"low|medium|high\", \"suggestion\": \"...\"}]
                
                Campaign data:
                {$context}"]
            ]
        ]
    ],
    'generationConfig' => [
        'responseMimeType' => 'application/json',
    ],
]);

$suggestions = json_decode($response->json('candidates.0.content.parts.0.text'), true);
```

---

### Module 3 — Campaign Builder

**Goal:** Build and publish complete Meta campaigns (Campaign + Ad Set + Ad) directly from YellowMedia using AI-generated images.

#### Database Additions

**`meta_ad_sets`**
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| tenant_id | bigint FK | |
| campaign_id | bigint FK | local campaign |
| ad_set_id | varchar | Meta Ad Set ID |
| name | varchar | |
| targeting | json | Age, gender, location, interests |
| placement | varchar | automatic / manual |
| daily_budget | decimal | |
| start_time | timestamp | |
| end_time | timestamp | nullable |
| status | varchar | |
| created_at / updated_at | timestamps | |

**`meta_ads`**
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| tenant_id | bigint FK | |
| ad_set_id | bigint FK | local ad set |
| ad_id | varchar | Meta Ad ID |
| name | varchar | |
| image_id | bigint FK | generated_images |
| headline | varchar | |
| body | text | Ad copy |
| caption | varchar | AI-generated post caption |
| cta | varchar | LEARN_MORE / SHOP_NOW / etc. |
| destination_url | varchar | |
| status | varchar | |
| created_at / updated_at | timestamps | |

#### Campaign Builder User Flow
1. User goes to **Campaign Builder → New Campaign**
2. **Step 1 — Campaign:**
   - Name, objective (Traffic / Conversions / Awareness / Engagement)
   - Budget type: Daily or Lifetime
3. **Step 2 — Ad Set:**
   - Audience: age range, gender, countries, interests
   - Placements: Automatic or manual (Feed, Stories, Reels)
   - Schedule: start date, optional end date
   - Daily budget
4. **Step 3 — Ad Creative:**
   - Pick image from Media Library OR generate new one inline
   - Headline, body copy
   - Click **"Generate Caption with AI"** → Gemini writes it
   - CTA button, destination URL
5. **Step 4 — Review & Launch**
   - Preview card showing how ad will look
   - Click **Launch** → pushed to Meta API
   - Status updated in real time

#### AI Caption Generation
```php
// On "Generate Caption" button click → POST /campaign-builder/generate-caption

$response = Http::withHeaders([
    'Content-Type' => 'application/json',
])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-pro-preview:generateContent?key=" . config('services.google.api_key'), [
    'contents' => [
        [
            'parts' => [
                ['text' => "Write a compelling social media ad caption for the following:
                Business: {$businessName}
                Product/Topic: {$topic}
                Objective: {$objective}
                Tone: {$tone}
                Language: {$language}
                Max length: 150 characters
                Include relevant emojis and a call to action."]
            ]
        ]
    ],
]);
```

#### Meta Campaign Creation API
```php
// app/Services/MetaAdsService.php

// 1. Create Campaign
$campaign = Http::post("https://graph.facebook.com/v19.0/act_{$accountId}/campaigns", [
    'name'              => $data['name'],
    'objective'         => $data['objective'], // e.g. OUTCOME_TRAFFIC
    'status'            => 'PAUSED', // always start paused, user activates
    'special_ad_categories' => [],
    'access_token'      => $token,
]);

// 2. Upload Image to Meta
$imageUpload = Http::attach('filename', $imageContents, 'ad_image.jpg')
    ->post("https://graph.facebook.com/v19.0/act_{$accountId}/adimages", [
        'access_token' => $token,
    ]);
$imageHash = $imageUpload->json('images.ad_image.jpg.hash');

// 3. Create Ad Set
$adSet = Http::post("https://graph.facebook.com/v19.0/act_{$accountId}/adsets", [
    'name'                  => $data['ad_set_name'],
    'campaign_id'           => $campaign->json('id'),
    'daily_budget'          => $data['daily_budget'] * 100, // in cents
    'billing_event'         => 'IMPRESSIONS',
    'optimization_goal'     => 'REACH',
    'targeting'             => $data['targeting'],
    'status'                => 'PAUSED',
    'access_token'          => $token,
]);

// 4. Create Ad Creative
$creative = Http::post("https://graph.facebook.com/v19.0/act_{$accountId}/adcreatives", [
    'name'          => $data['ad_name'] . ' Creative',
    'object_story_spec' => [
        'page_id'   => $data['page_id'],
        'link_data' => [
            'image_hash'  => $imageHash,
            'link'        => $data['destination_url'],
            'message'     => $data['body'],
            'name'        => $data['headline'],
            'call_to_action' => ['type' => $data['cta']],
        ],
    ],
    'access_token'  => $token,
]);

// 5. Create Ad
$ad = Http::post("https://graph.facebook.com/v19.0/act_{$accountId}/ads", [
    'name'       => $data['ad_name'],
    'adset_id'   => $adSet->json('id'),
    'creative'   => ['creative_id' => $creative->json('id')],
    'status'     => 'PAUSED',
    'access_token' => $token,
]);
```

---

### Module 4 — AI Campaign Review Engine

**Goal:** After a campaign has been running for at least 3 days, Gemini automatically reviews performance and generates actionable recommendations. User can approve changes which are pushed directly to Meta.

#### Review Triggers
- Scheduled: every 6 hours via `ReviewCampaignsJob`
- Manual: user clicks "Run AI Review" on campaign detail page
- Minimum data requirement: campaign must have run for **3+ days** with **$5+ spend**

#### What AI Reviews

| Signal | Threshold | Suggested Action |
|---|---|---|
| CTR < 1% | Poor engagement | Suggest new headline or creative |
| CPC > benchmark | Overspending per click | Suggest audience narrowing or bid cap |
| ROAS < 1 | Losing money | Suggest pausing or restructuring |
| Frequency > 3 | Ad fatigue | Suggest refreshing creative from Media Studio |
| One ad set outperforming | Budget opportunity | Suggest shifting budget to winner |
| Campaign running > 14 days | Staleness check | Full creative refresh recommendation |

#### AI Review Prompt (Gemini 3.1 Pro)
```php
$prompt = "You are a senior Meta Ads strategist. Analyze this campaign performance data 
and return ONLY valid JSON with this structure:
{
  \"overall_health\": \"good|warning|critical\",
  \"summary\": \"2-sentence plain English summary\",
  \"actions\": [
    {
      \"type\": \"pause_ad|increase_budget|decrease_budget|refresh_creative|change_audience|change_copy\",
      \"target\": \"campaign|ad_set|ad\",
      \"target_id\": \"local DB id\",
      \"reason\": \"plain English reason\",
      \"priority\": \"low|medium|high\",
      \"meta_change\": { /* actual Meta API params to apply if approved */ }
    }
  ]
}

Campaign data:
{$fullCampaignJson}";
```

#### Approve & Apply Flow
1. AI Review results shown on campaign detail page
2. Each recommended action shown as a card with reason + priority badge
3. User clicks **"Apply"** on any action
4. Laravel calls Meta API with the `meta_change` params from the AI response
5. Action logged to `ai_action_log` table with before/after values
6. User clicks **"Refresh Creative"** → redirected to Media Studio with campaign context pre-filled

#### Database Addition: `ai_action_log`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| tenant_id | bigint FK | |
| campaign_id | bigint FK | |
| action_type | varchar | pause_ad, increase_budget, etc. |
| target | varchar | campaign / ad_set / ad |
| target_meta_id | varchar | Meta entity ID |
| reason | text | AI explanation |
| before_value | json | State before change |
| after_value | json | State after change |
| applied_by | bigint FK | users.id |
| applied_at | timestamp | |

---

## Authentication — Laravel UI

### Installation
```bash
composer require laravel/ui
php artisan ui bootstrap --auth
npm install && npm run build
```

### Auth Routes (auto-registered by Laravel UI)
- `GET  /login` — Login form
- `POST /login` — Authenticate
- `POST /logout` — Logout
- `GET  /register` — Registration form (disabled for public, admin-only)
- `GET  /password/reset` — Forgot password

### Registration Policy
- Public self-registration is **disabled**
- Only `super_admin` can create new tenants and tenant admin users
- Tenant admins can invite team members within their tenant

### Middleware
```php
// In routes/web.php
Route::middleware(['auth'])->group(function () {
    // All dashboard routes
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    // Super admin only routes
});
```

### Custom Middleware: `CheckRole`
```php
// app/Http/Middleware/CheckRole.php
public function handle($request, Closure $next, $role)
{
    if (auth()->user()->role !== $role) {
        abort(403);
    }
    return $next($request);
}
```

### Tenant Scoping Middleware: `ScopeTenant`
- Automatically sets the current tenant context from `auth()->user()->tenant_id`
- Applied globally to all authenticated routes
- Prevents cross-tenant data access

---

## Project Structure

```
yellowmedia/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                  # Laravel UI auth controllers
│   │   │   ├── DashboardController.php
│   │   │   ├── MediaStudioController.php
│   │   │   ├── MetaAdsController.php
│   │   │   ├── CampaignController.php
│   │   │   ├── SuggestionController.php
│   │   │   └── SettingsController.php
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       └── ScopeTenant.php
│   ├── Jobs/
│   │   ├── GenerateImageJob.php
│   │   ├── SyncMetaCampaignsJob.php
│   │   ├── GenerateAdSuggestionsJob.php
│   │   └── ReviewCampaignsJob.php
│   ├── Models/
│   │   ├── Tenant.php
│   │   ├── User.php
│   │   ├── GeneratedImage.php
│   │   ├── MetaAdAccount.php
│   │   ├── MetaCampaign.php
│   │   └── AiSuggestion.php
│   └── Services/
│       ├── GeminiImageService.php
│       ├── GeminiTextService.php
│       └── MetaAdsService.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php          # Main authenticated layout
│       │   └── auth.blade.php         # Auth pages layout
│       ├── auth/                      # Laravel UI auth views
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── media-studio/
│       │   ├── index.blade.php        # Generator form
│       │   └── library.blade.php     # Saved images
│       ├── meta-ads/
│       │   ├── index.blade.php        # Ad accounts list
│       │   ├── dashboard.blade.php    # Campaigns overview
│       │   └── campaign.blade.php     # Campaign detail + suggestions + AI review
│       ├── campaign-builder/
│       │   ├── index.blade.php        # All campaigns list
│       │   ├── create.blade.php       # Step 1-4 wizard
│       │   └── show.blade.php         # Campaign detail + AI review
│       └── settings/
│           ├── index.blade.php
│           └── ad-accounts.blade.php
├── routes/
│   └── web.php
└── database/
    └── migrations/
```

---

## Routes Overview

```php
// routes/web.php

// Auth (Laravel UI)
Auth::routes(['register' => false]);

// Authenticated routes
Route::middleware(['auth', 'scope.tenant'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Media Studio
    Route::prefix('media-studio')->name('media.')->group(function () {
        Route::get('/',          [MediaStudioController::class, 'index'])->name('index');
        Route::post('/generate', [MediaStudioController::class, 'generate'])->name('generate');
        Route::get('/library',   [MediaStudioController::class, 'library'])->name('library');
        Route::get('/status/{id}', [MediaStudioController::class, 'status'])->name('status');
        Route::delete('/{id}',   [MediaStudioController::class, 'destroy'])->name('destroy');
    });

    // Campaign Builder
    Route::prefix('campaign-builder')->name('campaigns.')->group(function () {
        Route::get('/',              [CampaignBuilderController::class, 'index'])->name('index');
        Route::get('/create',        [CampaignBuilderController::class, 'create'])->name('create');
        Route::post('/',             [CampaignBuilderController::class, 'store'])->name('store');
        Route::get('/{id}',          [CampaignBuilderController::class, 'show'])->name('show');
        Route::post('/generate-caption', [CampaignBuilderController::class, 'generateCaption'])->name('caption');
        Route::post('/{id}/launch',  [CampaignBuilderController::class, 'launch'])->name('launch');
        Route::post('/{id}/review',  [CampaignBuilderController::class, 'review'])->name('review');
        Route::post('/actions/{id}/apply', [CampaignBuilderController::class, 'applyAction'])->name('action.apply');
    });


    Route::prefix('meta-ads')->name('meta.')->group(function () {
        Route::get('/',                        [MetaAdsController::class, 'index'])->name('index');
        Route::get('/connect',                 [MetaAdsController::class, 'connect'])->name('connect');
        Route::get('/callback',                [MetaAdsController::class, 'callback'])->name('callback');
        Route::get('/dashboard',               [MetaAdsController::class, 'dashboard'])->name('dashboard');
        Route::get('/campaigns/{id}',          [CampaignController::class, 'show'])->name('campaign.show');
        Route::post('/sync',                   [MetaAdsController::class, 'sync'])->name('sync');
        Route::post('/suggestions/{id}/dismiss', [SuggestionController::class, 'dismiss'])->name('suggestion.dismiss');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',             [SettingsController::class, 'index'])->name('index');
        Route::get('/ad-accounts',  [SettingsController::class, 'adAccounts'])->name('ad-accounts');
        Route::delete('/ad-accounts/{id}', [SettingsController::class, 'removeAdAccount'])->name('ad-accounts.remove');
    });

    // Super Admin
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('tenants', TenantController::class);
        Route::resource('users',   UserController::class);
    });
});
```

---

## Scheduled Jobs

```php
// app/Console/Kernel.php
$schedule->job(new SyncMetaCampaignsJob)->everySixHours();
$schedule->job(new GenerateAdSuggestionsJob)->everySixHours()->delay(30);
$schedule->job(new ReviewCampaignsJob)->everySixHours()->delay(60);
```

---

## Environment Variables

```env
APP_NAME=YellowMedia
APP_URL=https://media.yellowbrand.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yellowmedia
DB_USERNAME=
DB_PASSWORD=

GOOGLE_AI_API_KEY=
GOOGLE_IMAGE_MODEL=gemini-3-pro-image-preview
GOOGLE_TEXT_MODEL=gemini-3.1-pro-preview

META_APP_ID=
META_APP_SECRET=
META_REDIRECT_URI="${APP_URL}/meta-ads/callback"

QUEUE_CONNECTION=database
```

---

## Development Phases

### Phase 1 — Foundation
- Laravel project setup with Laravel UI auth
- Multi-tenant DB schema and migrations
- Sidebar layout (Blade) consistent with YellowPOS
- Super admin panel: manage tenants and users

### Phase 2 — Media Studio
- Gemini 3 Pro Image integration via REST API
- Image generation form (pure Blade + vanilla JS polling)
- Media library with download and delete
- Queue worker setup

### Phase 3 — Campaign Builder
- Meta OAuth connection flow
- 4-step campaign creation wizard (Blade)
- AI caption generation with Gemini 3.1 Pro
- Image picker from Media Library
- Meta API: create campaign → ad set → ad creative → ad
- Campaign list and detail pages

### Phase 4 — Ads Monitor & AI Review
- Campaign sync job (every 6 hours)
- Ads dashboard with metrics
- AI suggestions engine (Gemini 3.1 Pro)
- AI Review Engine with approve & apply flow
- Action log (before/after tracking)
- "Refresh Creative" shortcut back to Media Studio

### Phase 5 — SaaS Polish
- Subscription plans (free / starter / pro)
- Usage limits per plan (image credits, campaign slots)
- Onboarding flow for new tenants
- Email notifications for sync errors, AI review results, high-priority suggestions
