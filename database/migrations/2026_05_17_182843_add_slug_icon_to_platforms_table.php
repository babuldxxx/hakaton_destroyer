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
    Schema::table('platforms', function (Blueprint $table) {
        if (!Schema::hasColumn('platforms', 'slug')) {
            $table->string('slug')->nullable()->unique()->after('name');
        }
        if (!Schema::hasColumn('platforms', 'icon')) {
            $table->string('icon')->nullable()->after('slug');
        }
    });
}

public function down(): void
{
    Schema::table('platforms', function (Blueprint $table) {
        if (Schema::hasColumn('platforms', 'slug')) {
            $table->dropColumn('slug');
        }
        if (Schema::hasColumn('platforms', 'icon')) {
            $table->dropColumn('icon');
        }
    });
}
};
