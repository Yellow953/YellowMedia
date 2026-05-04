<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_ad_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('meta_campaigns')->cascadeOnDelete();
            $table->string('ad_set_id')->nullable();
            $table->string('name');
            $table->json('targeting')->nullable();
            $table->string('placement')->default('automatic');
            $table->decimal('daily_budget', 12, 2)->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('status')->default('PAUSED');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_ad_sets');
    }
};
