<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kartu_pas', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nama_pemegang');
        });
    }

    public function down(): void
    {
        Schema::table('kartu_pas', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};