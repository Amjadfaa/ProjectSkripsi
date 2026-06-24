<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kartu_pas', function (Blueprint $table) {
            $table->enum('keterangan_nonaktif', ['resign', 'pensiun', 'meninggal', 'lainnya'])->nullable()->after('status');
            $table->text('catatan_nonaktif')->nullable()->after('keterangan_nonaktif');
        });
    }

    public function down(): void
    {
        Schema::table('kartu_pas', function (Blueprint $table) {
            $table->dropColumn(['keterangan_nonaktif', 'catatan_nonaktif']);
        });
    }
};