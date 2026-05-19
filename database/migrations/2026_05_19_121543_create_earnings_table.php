<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();

            // Привязка
            $table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
            $table->foreignId('platform_id')->nullable()->constrained('platforms')->nullOnDelete();
            $table->foreignId('royalty_report_id')->nullable()->constrained('royalty_reports')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users'); // кто внёс (лейбл)

            // Деньги и период
            $table->string('period'); // формат: 2026-05
            $table->decimal('gross_amount', 14, 2); // вся сумма от дистрибьютора
            $table->decimal('label_share_percent', 5, 2)->default(0); // доля лейбла, например 20.00
            $table->string('currency', 3)->default('RUB');

            // Статус
            $table->string('status')->default('pending'); // pending | distributed | error

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};