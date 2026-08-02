<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kartu PAS {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { color: #1e3a5f; font-size: 16px; margin: 0; }
        .header p { color: #64748b; font-size: 11px; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead { background: #1e3a5f; color: white; }
        th { padding: 8px 6px; text-align: center; font-size: 10px; }
        th:first-child { text-align: left; }
        td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; font-size: 10px; text-align: center; }
        td:first-child { text-align: left; font-weight: bold; }
        tr:nth-child(even) { background: #f8fafc; }
        tfoot td { background: #e2e8f0; font-weight: bold; border-top: 2px solid #1e3a5f; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KARTU PAS BULANAN MONPASKU</h2>
        <p>Sistem Monitoring PAS Bandara &nbsp;|&nbsp; Tahun {{ $tahun }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

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
            <tr>
                <td>{{ \DateTime::createFromFormat('!m', $laporan->bulan)->format('F') }} {{ $laporan->tahun }}</td>
                <td>{{ $laporan->kartu_baru }}</td>
                <td>{{ $laporan->kartu_diperpanjang }}</td>
                <td>{{ $laporan->kartu_kadaluarsa }}</td>
                <td><strong>{{ $laporan->total_terbit }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL PER TAHUN</td>
                <td>{{ $laporanKartu->sum('kartu_baru') }}</td>
                <td>{{ $laporanKartu->sum('kartu_diperpanjang') }}</td>
                <td>{{ $laporanKartu->sum('kartu_kadaluarsa') }}</td>
                <td>{{ $laporanKartu->sum('total_terbit') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem MONPASKU
    </div>
</body>
</html>