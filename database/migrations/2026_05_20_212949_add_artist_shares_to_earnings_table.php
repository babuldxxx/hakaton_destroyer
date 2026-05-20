<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->string('artist_shares')->nullable()->after('label_share_percent');
        });
    }
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropColumn('artist_shares');
        });
    }
};