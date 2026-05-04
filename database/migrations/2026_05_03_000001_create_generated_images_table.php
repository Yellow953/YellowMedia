<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('prompt');
            $table->text('revised_prompt')->nullable();
            $table->enum('format', ['post', 'story', 'banner'])->default('post');
            $table->string('size')->nullable();
            $table->string('file_path')->nullable();
            $table->string('google_generation_id')->nullable();
            $table->enum('status', ['pending', 'done', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_images');
    }
};
