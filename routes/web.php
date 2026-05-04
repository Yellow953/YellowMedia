<?php

use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CampaignBuilderController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MediaStudioController;
use App\Http\Controllers\MetaAdsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SuggestionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::middleware(['auth', 'scope.tenant'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Media Studio
    Route::prefix('media-studio')->name('media.')->group(function () {
        Route::get('/', [MediaStudioController::class, 'index'])->name('index');
        Route::post('/generate', [MediaStudioController::class, 'generate'])->name('generate');
        Route::get('/library', [MediaStudioController::class, 'library'])->name('library');
        Route::get('/status/{id}', [MediaStudioController::class, 'status'])->name('status');
        Route::delete('/{id}', [MediaStudioController::class, 'destroy'])->name('destroy');
    });

    // Campaign Builder
    Route::prefix('campaign-builder')->name('campaigns.')->group(function () {
        Route::get('/', [CampaignBuilderController::class, 'index'])->name('index');
        Route::get('/create', [CampaignBuilderController::class, 'create'])->name('create');
        Route::post('/', [CampaignBuilderController::class, 'store'])->name('store');
        Route::get('/{id}', [CampaignBuilderController::class, 'show'])->name('show');
        Route::post('/generate-caption', [CampaignBuilderController::class, 'generateCaption'])->name('caption');
        Route::post('/{id}/launch', [CampaignBuilderController::class, 'launch'])->name('launch');
        Route::post('/{id}/review', [CampaignBuilderController::class, 'review'])->name('review');
        Route::post('/actions/{id}/apply', [CampaignBuilderController::class, 'applyAction'])->name('action.apply');
    });

    // Meta Ads Monitor
    Route::prefix('meta-ads')->name('meta.')->group(function () {
        Route::get('/', [MetaAdsController::class, 'index'])->name('index');
        Route::get('/connect', [MetaAdsController::class, 'connect'])->name('connect');
        Route::get('/callback', [MetaAdsController::class, 'callback'])->name('callback');
        Route::get('/dashboard', [MetaAdsController::class, 'dashboard'])->name('dashboard');
        Route::get('/campaigns/{id}', [CampaignController::class, 'show'])->name('campaign.show');
        Route::post('/sync', [MetaAdsController::class, 'sync'])->name('sync');
        Route::post('/suggestions/{id}/dismiss', [SuggestionController::class, 'dismiss'])->name('suggestion.dismiss');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/ad-accounts', [SettingsController::class, 'adAccounts'])->name('ad-accounts');
        Route::delete('/ad-accounts/{id}', [SettingsController::class, 'removeAdAccount'])->name('ad-accounts.remove');
    });

    // Super Admin
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('tenants', TenantController::class);
        Route::resource('users', UserController::class);
    });
});
