<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            // Удаляем кривой внешний ключ
            $table->dropForeign(['label_id']);
        });
    }

    public function down(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            // Если откатываешь — можно вернуть (но на таблицу labels, как было)
            $table->foreign('label_id')->references('id')->on('labels')->nullOnDelete();
        });
    }
};
