<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Aktivitas Area Akses</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 15px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { color: #1e3a5f; font-size: 15px; margin: 0; }
        .header p { color: #64748b; font-size: 10px; margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead { background: #1e3a5f; color: white; }
        th { padding: 6px; text-align: left; font-size: 9px; }
        td { padding: 6px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        tr:nth-child(even) { background: #f8fafc; }
        .badge-diterima { background: #dcfce7; color: #15803d; padding: 2px 6px; border-radius: 4px; font-weight: bold; }
        .badge-ditolak { background: #ffe4e6; color: #be123c; padding: 2px 6px; border-radius: 4px; font-weight: bold; }
        .footer { margin-top: 15px; font-size: 8px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN AKTIVITAS KELUAR / MASUK AREA AKSES</h2>
        <p>Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }} @if($kodeArea) | Area: {{ $kodeArea }} @endif</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 11%;">Waktu Scan</th>
                <th style="width: 14%;">No. Kartu PAS</th>
                <th style="width: 18%;">Nama Pemegang</th>
                <th style="width: 18%;">Perusahaan / Instansi</th>
                <th style="width: 9%;">Area</th>
                <th style="width: 10%;">Aktivitas</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 12%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->waktu_scan ? $log->waktu_scan->format('d/m/Y H:i') : '-' }}</td>
                <td><strong>{{ $log->nomor_kartu }}</strong></td>
                <td>{{ $log->nama_pemegang }}</td>
                <td>{{ $log->perusahaan }}</td>
                <td>Area {{ $log->kode_area }}</td>
                <td>
                    <span class="badge-diterima" style="background: {{ $log->tipe_aktivitas === 'keluar' ? '#fef3c7; color: #92400e;' : '#dcfce7; color: #15803d;' }}">
                        {{ strtoupper($log->tipe_aktivitas ?: 'MASUK') }}
                    </span>
                </td>
                <td>
                    <span class="{{ $log->status_akses === 'diterima' ? 'badge-diterima' : 'badge-ditolak' }}">
                        {{ strtoupper($log->status_akses) }}
                    </span>
                </td>
                <td>
                    {{ $log->alasan }}
                    @if($log->catatan)
                        <br><span style="color: #1d4ed8; font-style: italic; font-size: 8px;">Catatan: {{ $log->catatan }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #94a3b8; padding: 15px;">Tidak ada data aktivitas scan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total {{ $logs->count() }} rekaman aktivitas. Dicetak otomatis oleh Sistem MONPASKU.
    </div>
</body>
</html>
