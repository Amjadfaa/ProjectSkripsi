<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kartu PAS {{ $namaBulan ? "$namaBulan $tahun" : $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; color: #1e293b; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { color: #1e3a5f; font-size: 16px; margin: 0; }
        .header p { color: #64748b; font-size: 11px; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        thead { background: #1e3a5f; color: white; }
        th { padding: 8px 6px; text-align: center; font-size: 10px; }
        th:first-child { text-align: left; }
        td { padding: 7px 6px; border-bottom: 1px solid #e2e8f0; font-size: 10px; text-align: center; }
        td:first-child { text-align: left; }
        tr:nth-child(even) { background: #f8fafc; }
        tfoot td { background: #e2e8f0; font-weight: bold; border-top: 2px solid #1e3a5f; }
        .section-title { font-size: 12px; font-weight: bold; color: #1e3a5f; margin-top: 20px; margin-bottom: 8px; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KARTU PAS BANDARA MONPASKU</h2>
        <p>Periode: {{ $namaBulan ? "$namaBulan $tahun" : "Tahun $tahun" }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <div class="section-title">Rekapitulasi Bulanan (Tahun {{ $tahun }})</div>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Kartu Baru Terbit</th>
                <th>Kartu Diperpanjang</th>
                <th>Kartu Kadaluarsa</th>
                <th>Total Terbit / Diperbarui</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanKartu as $laporan)
            <tr style="{{ ($bulan !== 'all' && (int)$bulan === (int)$laporan->bulan) ? 'background-color: #e0f2fe; font-weight: bold;' : '' }}">
                <td>{{ \DateTime::createFromFormat('!m', $laporan->bulan)->format('F') }} {{ $laporan->tahun }}</td>
                <td>{{ number_format($laporan->kartu_baru) }}</td>
                <td>{{ number_format($laporan->kartu_diperpanjang) }}</td>
                <td>{{ number_format($laporan->kartu_kadaluarsa) }}</td>
                <td><strong>{{ number_format($laporan->total_terbit) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL PER TAHUN</td>
                <td>{{ number_format($laporanKartu->sum('kartu_baru')) }}</td>
                <td>{{ number_format($laporanKartu->sum('kartu_diperpanjang')) }}</td>
                <td>{{ number_format($laporanKartu->sum('kartu_kadaluarsa')) }}</td>
                <td>{{ number_format($laporanKartu->sum('total_terbit')) }}</td>
            </tr>
        </tfoot>
    </table>

    @if(!empty($detailKartu) && count($detailKartu) > 0)
    <div class="section-title" style="page-break-before: always;">
        Daftar Rincian Kartu PAS - Bulan {{ $namaBulan }} {{ $tahun }} (Total: {{ count($detailKartu) }} kartu)
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>No. Kartu PAS</th>
                <th>Nama Pemegang</th>
                <th>Instansi / Perusahaan</th>
                <th>Jabatan</th>
                <th>Area</th>
                <th>Masa Berlaku</th>
                <th>Tipe</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailKartu as $i => $k)
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $k->nomor_kartu }}</td>
                <td style="text-align: left; font-weight: bold;">{{ $k->nama_pemegang }}</td>
                <td style="text-align: left;">{{ $k->perusahaan ?? ($k->instansi->nama_instansi ?? '-') }}</td>
                <td style="text-align: left;">{{ $k->jabatan ?? '-' }}</td>
                <td>{{ $k->area_akses ?? '-' }}</td>
                <td>{{ $k->tanggal_berlaku ? $k->tanggal_berlaku->format('d/m/Y') : '-' }}</td>
                <td>{{ ucfirst($k->tipe_permohonan ?? 'baru') }}</td>
                <td>{{ ucfirst($k->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Dicetak secara otomatis oleh Sistem MONPASKU
    </div>
</body>
</html>