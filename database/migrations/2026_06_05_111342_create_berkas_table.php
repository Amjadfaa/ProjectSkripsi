<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan')->onDelete('cascade');
            $table->string('nama_berkas');
            $table->string('file_path');
            $table->enum('status', ['belum_diverifikasi', 'diverifikasi', 'ditolak'])->default('belum_diverifikasi');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas');
    }
};