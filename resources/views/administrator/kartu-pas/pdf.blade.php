<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kartu PAS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h2 { text-align: center; color: #1e3a5f; margin-bottom: 4px; }
        p.subtitle { text-align: center; color: #666; margin-bottom: 15px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #1e3a5f; color: white; }
        th { padding: 6px 5px; text-align: left; font-size: 10px; }
        td { padding: 5px 5px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: middle; }
        tr:nth-child(even) { background: #f8fafc; }
        .status-aktif { color: #16a34a; font-weight: bold; }
        .status-kadaluarsa { color: #dc2626; font-weight: bold; }
        .status-tidak_aktif { color: #6b7280; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 10px; color: #94a3b8; text-align: right; }
        .qr-code { text-align: center; }
    </style>
</head>
<body>

    <h2>LAPORAN DATA KARTU PAS</h2>
    <p class="subtitle">MONPASKU - Sistem Monitoring PAS Bandara &nbsp;|&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 50px; text-align: center;">QR Code</th>
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
            @php
                $qrOptions = new \chillerlan\QRCode\QROptions;
                $qrOptions->outputInterface = \chillerlan\QRCode\Output\QRGdImagePNG::class;
                $qrOptions->scale = 3;
                $qrOptions->imageTransparent = false;
                $qrOptions->outputBase64 = false;

                $qr = new \chillerlan\QRCode\QRCode($qrOptions);
                $pngData = $qr->render($kartu->nomor_kartu);
                $qrBase64 = base64_encode($pngData);
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="qr-code">
                    <img src="data:image/png;base64,{{ $qrBase64 }}" width="40" height="40">
                </td>
                <td><strong>{{ $kartu->nomor_kartu }}</strong></td>
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