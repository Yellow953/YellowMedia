<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ad_set_id')->constrained('meta_ad_sets')->cascadeOnDelete();
            $table->string('ad_id')->nullable();
            $table->string('name');
            $table->foreignId('image_id')->nullable()->constrained('generated_images')->nullOnDelete();
            $table->string('headline')->nullable();
            $table->text('body')->nullable();
            $table->string('caption')->nullable();
            $table->string('cta')->nullable();
            $table->string('destination_url')->nullable();
            $table->string('status')->default('PAUSED');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_ads');
    }
};
