<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('kartu_pas', 'keterangan')) {
            Schema::table('kartu_pas', function (Blueprint $table) {
                $table->string('keterangan', 255)->nullable()->after('tipe_permohonan');
            });

            // Sinkronisasi data awal dari tipe_permohonan jika ada
            DB::table('kartu_pas')->whereNull('keterangan')->update([
                'keterangan' => DB::raw("CASE 
                    WHEN LOWER(tipe_permohonan) = 'perpanjangan' THEN 'Perpanjangan'
                    ELSE 'Baru'
                END")
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kartu_pas', 'keterangan')) {
            Schema::table('kartu_pas', function (Blueprint $table) {
                $table->dropColumn('keterangan');
            });
        }
    }
};
