<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->enum('tipe_aktivitas', ['masuk', 'keluar'])->default('masuk')->after('kode_area');
        });
    }

    public function down(): void
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->dropColumn('tipe_aktivitas');
        });
    }
};
