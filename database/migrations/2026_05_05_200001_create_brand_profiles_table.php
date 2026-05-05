<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('business_name')->nullable();
            $table->text('business_description')->nullable();
            $table->string('target_audience')->nullable();
            $table->string('tone', 50)->default('professional');
            $table->json('content_pillars')->nullable();
            $table->string('brand_colors')->nullable();
            $table->text('hashtags')->nullable();
            $table->string('instagram_handle', 100)->nullable();
            $table->text('sample_captions')->nullable();
            $table->text('voice_summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_profiles');
    }
};
