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
        if (!Schema::hasColumn('kartu_pas', 'tipe_permohonan')) {
            Schema::table('kartu_pas', function (Blueprint $table) {
                $table->string('tipe_permohonan', 50)->default('baru')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('kartu_pas', 'tipe_permohonan')) {
            Schema::table('kartu_pas', function (Blueprint $table) {
                $table->dropColumn('tipe_permohonan');
            });
        }
    }
};
