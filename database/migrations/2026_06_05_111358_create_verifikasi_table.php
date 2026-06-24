<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berkas_id')->constrained('berkas')->onDelete('cascade');
            $table->foreignId('verifikator_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_verified')->default(false);
            $table->text('catatan')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi');
    }
};