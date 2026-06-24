<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kartu PAS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; color: #1e3a5f; margin-bottom: 4px; }
        p.subtitle { text-align: center; color: #666; margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #1e3a5f; color: white; }
        th { padding: 8px 6px; text-align: left; font-size: 11px; }
        td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tr:nth-child(even) { background: #f8fafc; }
        .status-aktif { color: #16a34a; font-weight: bold; }
        .status-kadaluarsa { color: #dc2626; font-weight: bold; }
        .status-tidak_aktif { color: #6b7280; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 10px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>

    <h2>LAPORAN DATA KARTU PAS</h2>
    <p class="subtitle">MONPASKU - Sistem Monitoring PAS Bandara &nbsp;|&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Kartu</th>
                <th>Nama Pemegang</th>
                <th>Instansi</th>
                <th>Area Akses</th>
                <th>Tgl Terbit</th>
                <th>Tgl Berlaku</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kartuPas as $i => $kartu)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $kartu->nomor_kartu }}</td>
                <td>{{ $kartu->nama_pemegang }}</td>
                <td>{{ $kartu->perusahaan }}</td>
                <td>{{ $kartu->area_akses }}</td>
                <td>{{ $kartu->tanggal_terbit->format('d/m/Y') }}</td>
                <td>{{ $kartu->tanggal_berlaku->format('d/m/Y') }}</td>
                <td class="status-{{ $kartu->status }}">{{ ucfirst(str_replace('_', ' ', $kartu->status)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total Data: {{ count($kartuPas) }} kartu
    </div>

</body>
</html>