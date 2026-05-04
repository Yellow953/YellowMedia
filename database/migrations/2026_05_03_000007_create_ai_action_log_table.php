<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_action_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('meta_campaigns')->cascadeOnDelete();
            $table->string('action_type');
            $table->string('target');
            $table->string('target_meta_id')->nullable();
            $table->text('reason')->nullable();
            $table->json('before_value')->nullable();
            $table->json('after_value')->nullable();
            $table->foreignId('applied_by')->constrained('users');
            $table->timestamp('applied_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_action_log');
    }
};
