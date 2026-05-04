<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ad_account_id')->constrained('meta_ad_accounts')->cascadeOnDelete();
            $table->string('campaign_id');
            $table->string('name');
            $table->string('status')->default('PAUSED');
            $table->string('objective')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('spend', 12, 2)->default(0);
            $table->bigInteger('impressions')->default(0);
            $table->bigInteger('clicks')->default(0);
            $table->decimal('ctr', 8, 4)->default(0);
            $table->decimal('cpc', 8, 4)->default(0);
            $table->decimal('roas', 8, 4)->default(0);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_campaigns');
    }
};
