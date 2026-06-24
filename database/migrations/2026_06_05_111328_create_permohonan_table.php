<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nomor_permohonan')->unique();
            $table->string('nama_pemohon');
            $table->string('perusahaan');
            $table->string('keperluan');
            $table->enum('status', ['menunggu', 'diproses', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan');
    }
};