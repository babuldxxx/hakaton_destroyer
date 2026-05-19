<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('royalty_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('label_id')->constrained('users'); // кто загрузил
            $table->string('distributor')->nullable(); // TuneCore, DistroKid...
            $table->string('report_period'); // 2026-05
            $table->string('filename')->nullable();
            $table->json('raw_data')->nullable(); // сырые данные CSV
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('royalty_reports');
    }
};