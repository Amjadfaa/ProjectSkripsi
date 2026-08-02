<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_device_id')->nullable()->constrained('camera_devices')->nullOnDelete();
            $table->string('kode_area');
            $table->string('nomor_kartu');
            $table->string('nama_pemegang')->nullable();
            $table->string('perusahaan')->nullable();
            $table->enum('status_akses', ['diterima', 'ditolak']);
            $table->string('alasan')->nullable();
            $table->timestamp('waktu_scan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
    }
};
