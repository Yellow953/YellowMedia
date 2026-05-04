<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('meta_campaigns')->cascadeOnDelete();
            $table->text('suggestion');
            $table->enum('type', ['budget', 'audience', 'creative', 'copy', 'general'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('is_dismissed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_suggestions');
    }
};
