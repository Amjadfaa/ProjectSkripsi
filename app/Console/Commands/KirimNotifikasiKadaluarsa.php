<?php

namespace App\Console\Commands;

use App\Mail\NotifikasiKadaluarsaMail;
use App\Models\Instansi;
use App\Models\KartuPas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class KirimNotifikasiKadaluarsa extends Command
{
    protected $signature   = 'notifikasi:kadaluarsa';
    protected $description = 'Kirim notifikasi email ke instansi untuk kartu PAS yang akan kadaluarsa';

    public function handle()
    {
        // Ambil semua kartu aktif yang akan kadaluarsa dalam 30 hari
        $kartuList = KartuPas::where('status', 'aktif')
            ->whereBetween('tanggal_berlaku', [now(), now()->addDays(30)])
            ->orderBy('tanggal_berlaku')
            ->get();

        if ($kartuList->isEmpty()) {
            $this->info('Tidak ada kartu PAS yang akan kadaluarsa dalam 30 hari.');
            return;
        }

        // Kelompokkan kartu berdasarkan instansi/perusahaan
        $kartuPerInstansi = $kartuList->groupBy('perusahaan');

        $terkirim = 0;
        $gagal    = 0;
        $skip     = 0;

        foreach ($kartuPerInstansi as $namaInstansi => $kartuGroup) {
            // Cari email instansi dari tabel instansis
            $instansi = Instansi::where('nama_instansi', $namaInstansi)->first();

            // Cari email dari user pemohon yang perusahaannya sama
            $emailPemohon = \App\Models\User::where('role', 'pemohon')
                ->where('perusahaan', $namaInstansi)
                ->pluck('email')
                ->toArray();

            // Gabungkan email instansi dan email pemohon
            $emails = collect($emailPemohon);
            if ($instansi && !empty($instansi->email)) {
                $emails->push($instansi->email);
            }
            $emails = $emails->unique()->filter()->values();

            if ($emails->isEmpty()) {
                $this->warn("⚠️ Skip instansi '{$namaInstansi}' - tidak ada email terdaftar");
                $skip++;
                continue;
            }

            try {
                foreach ($emails as $email) {
                    Mail::to($email)->send(new NotifikasiKadaluarsaMail($kartuGroup, $namaInstansi));
                    $this->info("✅ Email terkirim ke {$email} untuk instansi '{$namaInstansi}' ({$kartuGroup->count()} kartu)");
                }
                $terkirim++;
            } catch (\Exception $e) {
                $gagal++;
                $this->error("❌ Gagal kirim ke instansi '{$namaInstansi}': " . $e->getMessage());
            }
        }

        $this->info("Selesai. Terkirim: {$terkirim}, Gagal: {$gagal}, Skip: {$skip}");
    }
}