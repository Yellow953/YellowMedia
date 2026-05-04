<?php

use App\Jobs\GenerateAdSuggestionsJob;
use App\Jobs\ReviewCampaignsJob;
use App\Jobs\SyncMetaCampaignsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SyncMetaCampaignsJob)->everySixHours();
Schedule::job(new GenerateAdSuggestionsJob)->cron('30 */6 * * *');
Schedule::job(new ReviewCampaignsJob)->cron('0 1,7,13,19 * * *');
