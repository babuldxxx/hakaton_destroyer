<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            if (! Schema::hasColumn('earnings', 'label_id')) {
                $table->foreignId('label_id')->nullable()->constrained('labels')->after('id');
            }
            if (! Schema::hasColumn('earnings', 'artist_shares')) {
                $table->string('artist_shares')->nullable()->after('label_share_percent');
            }
            if (! Schema::hasColumn('earnings', 'raw_track_name')) {
                $table->string('raw_track_name')->nullable();
                $table->string('raw_artist_name')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropColumnIfExists('label_id');
            $table->dropColumnIfExists('artist_shares');
            $table->dropColumnIfExists('raw_track_name');
            $table->dropColumnIfExists('raw_artist_name');
        });
    }
};