<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'earning_id')) {
                $table->foreignId('earning_id')->nullable()->constrained('earnings')->nullOnDelete();
            }

            if (!Schema::hasColumn('transactions', 'type')) {
                $table->string('type')->default('author_rights');
            }

            if (!Schema::hasColumn('transactions', 'status')) {
                $table->string('status')->default('pending');
            }

            if (!Schema::hasColumn('transactions', 'period')) {
                $table->string('period'); // ← убран after('status')
            }

            if (!Schema::hasColumn('transactions', 'meta')) {
                $table->json('meta')->nullable(); // ← убран after('period')
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'earning_id')) {
                $table->dropForeign(['earning_id']);
                $table->dropColumn('earning_id');
            }
            if (Schema::hasColumn('transactions', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('transactions', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('transactions', 'period')) {
                $table->dropColumn('period');
            }
            if (Schema::hasColumn('transactions', 'meta')) {
                $table->dropColumn('meta');
            }
        });
    }
};
