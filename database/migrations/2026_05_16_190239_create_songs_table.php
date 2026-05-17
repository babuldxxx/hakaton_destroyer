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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('lyrics')->nullable();
            $table->date('written_at')->nullable();
            $table->date('released_at')->nullable();
            $table->foreignId('label_id')->constrained('labels');
            $table->string('wav_path')->nullable();
            $table->string('mp3_path')->nullable();
            $table->string('isrc', 12)->nullable()->unique();
            $table->foreignId('genre_id')->nullable()->constrained('genres')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
