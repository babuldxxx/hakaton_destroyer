<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Добавляем колонку
            $table->string('nickname')->nullable()->after('name');
        });

        // 2. Заполняем существующих пользователей (берём часть email до @)
        DB::table('users')->cursor()->each(function ($user) {
            $nick = explode('@', $user->email)[0] . '_' . $user->id;
            DB::table('users')->where('id', $user->id)->update(['nickname' => $nick]);
        });

        Schema::table('users', function (Blueprint $table) {
            // 3. Делаем обязательным и уникальным
            $table->string('nickname')->nullable(false)->change();
            $table->unique('nickname');

            // 4. Убираем уникальность с email
            $table->dropUnique(['email']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nickname');
            $table->unique('email');
        });
    }
};