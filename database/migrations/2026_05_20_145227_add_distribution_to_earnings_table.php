<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->foreignId('artist_id')->nullable()->after('song_id')->constrained()->nullOnDelete();
            $table->foreignId('label_id')->nullable()->after('artist_id')->constrained('labels')->nullOnDelete();

            // Рассчитанные суммы (чтобы не считать на лету в SQL)
            $table->decimal('artist_amount', 14, 2)->nullable()->after('gross_amount');
            $table->decimal('label_amount', 14, 2)->nullable()->after('artist_amount');

            // Если трек/артист не найдены в базе — сохраняем исходные строки
            $table->string('raw_track_name')->nullable()->after('currency');
            $table->string('raw_artist_name')->nullable()->after('raw_track_name');
        });
    }

    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign(['artist_id']);
            $table->dropForeign(['label_id']);
            $table->dropColumn(['artist_id', 'label_id', 'artist_amount', 'label_amount', 'raw_track_name', 'raw_artist_name']);
        });
    }
};