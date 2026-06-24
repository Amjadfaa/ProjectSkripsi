<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas_persyaratan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_berkas');
            $table->string('file_path');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas_persyaratan');
    }
};