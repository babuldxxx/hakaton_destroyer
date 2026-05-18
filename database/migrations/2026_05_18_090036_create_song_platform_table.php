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
    Schema::create('song_platform', function (Blueprint $table) {
        $table->foreignId('song_id')->constrained()->cascadeOnDelete();
        $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
        $table->primary(['song_id', 'platform_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_platform');
    }
};
