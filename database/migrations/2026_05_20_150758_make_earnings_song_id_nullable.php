<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign(['song_id']);
            $table->unsignedBigInteger('song_id')->nullable()->change();
            $table->foreign('song_id')->references('id')->on('songs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign(['song_id']);
            $table->unsignedBigInteger('song_id')->nullable(false)->change();
            $table->foreign('song_id')->references('id')->on('songs')->cascadeOnDelete();
        });
    }
};