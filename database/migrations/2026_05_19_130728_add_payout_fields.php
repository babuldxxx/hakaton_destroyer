<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            if (!Schema::hasColumn('payouts', 'artist_id')) {
                $table->foreignId('artist_id')->nullable()->constrained();
            }
            if (!Schema::hasColumn('payouts', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payouts', 'currency')) {
                $table->string('currency', 3)->default('RUB');
            }
            if (!Schema::hasColumn('payouts', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('payouts', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
            if (!Schema::hasColumn('payouts', 'method')) {
                $table->string('method')->nullable();
            }
            if (!Schema::hasColumn('payouts', 'details')) {
                $table->text('details')->nullable();
            }
            if (!Schema::hasColumn('payouts', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users');
            }
        });

        // Связь transactions → payouts
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'payout_id')) {
                $table->foreignId('payout_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            // оставь пустым или удали вручную, если что
        });
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'payout_id')) {
                $table->dropForeign(['payout_id']);
                $table->dropColumn('payout_id');
            }
        });
    }
};