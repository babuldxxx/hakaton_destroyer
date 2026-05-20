<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artist_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('artist_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email');
            $table->string('token', 64)->unique();
            $table->string('status')->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_invitations');
    }
};