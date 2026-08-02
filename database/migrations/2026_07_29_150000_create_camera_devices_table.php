<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camera_devices', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kamera');          // e.g. "Kamera Gate 1 - Kedatangan"
            $table->string('kode_area');            // e.g. "A" (from area_akses)
            $table->string('kode_akses')->unique(); // Unique passcode for camera login, e.g. "CAM-A01"
            $table->enum('tipe_scan', ['masuk', 'keluar', 'masuk_keluar'])->default('masuk_keluar');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camera_devices');
    }
};
