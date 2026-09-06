<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah role enum di users hanya menjadi administrator dan operator
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'operator') NOT NULL DEFAULT 'operator'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pemohon', 'administrator', 'verifikator') NOT NULL DEFAULT 'pemohon'");
    }
};
