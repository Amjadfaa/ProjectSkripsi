<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_pas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan')->onDelete('cascade');
            $table->string('nomor_kartu')->unique();
            $table->string('nama_pemegang');
            $table->string('perusahaan');
            $table->string('area_akses');
            $table->date('tanggal_terbit');
            $table->date('tanggal_berlaku');
            $table->enum('status', ['aktif', 'tidak_aktif', 'kadaluarsa'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu_pas');
    }
};