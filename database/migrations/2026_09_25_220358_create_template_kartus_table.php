<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('template_kartus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template');
            $table->string('kode_warna')->default('kuning'); // e.g. kuning, merah, biru, custom
            $table->string('warna_label')->nullable();        // e.g. "Kuning (Terminal)", "Merah (Apron)"
            $table->string('warna_hex')->default('#FACC15');  // Default yellow #FACC15
            $table->string('warna_teks')->default('#000000'); // Hex text color
            $table->string('gambar_template');                // Relative path in storage
            $table->json('area_akses')->nullable();           // Array of area codes e.g. ["A", "B", "C"]
            $table->integer('prioritas')->default(1);         // Higher priority takes precedence
            $table->boolean('is_default')->default(false);    // Fallback template
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->json('posisi_pengaturan')->nullable();    // Layout / coordinate / display settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_kartus');
    }
};
