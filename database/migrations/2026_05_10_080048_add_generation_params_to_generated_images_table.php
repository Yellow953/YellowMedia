<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('generated_images', function (Blueprint $table) {
            $table->json('generation_params')->nullable()->after('caption');
        });
    }

    public function down(): void
    {
        Schema::table('generated_images', function (Blueprint $table) {
            $table->dropColumn('generation_params');
        });
    }
};
