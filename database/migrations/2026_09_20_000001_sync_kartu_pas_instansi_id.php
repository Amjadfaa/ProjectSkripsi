<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\KartuPas;
use App\Models\Instansi;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Instansi::syncUnlinkedKartuPas();
        } catch (\Throwable $e) {
            // Silently handle if tables are not yet present during fresh migrations
        }
    }

    public function down(): void
    {
        // No down operation needed
    }
};
