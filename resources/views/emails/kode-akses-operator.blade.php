@component('mail::message')
# 🔑 Otorisasi & Penugasan Kode Akses Kamera

Halo **{{ $user->name }}**,

Administrator sistem **MONPASKU** telah memperbarui hak otorisasi dan menugaskan perangkat kamera scanner untuk akun Anda.

Berikut adalah daftar titik perangkat kamera beserta **Kode Akses Login** yang boleh Anda akses:

@component('mail::table')
| Perangkat Kamera | Area Akses | Tipe Scan | Kode Akses |
|:-----------------|:-----------|:----------|:-----------|
@forelse($cameras as $cam)
| **{{ $cam->nama_kamera }}** | {{ $cam->kode_area }} ({{ optional($cam->areaAkses)->keterangan ?? '-' }}) | {{ strtoupper(str_replace('_', ' ', $cam->tipe_scan)) }} | `{{ $cam->kode_akses }}` |
@empty
| *(Belum ada kamera ditugaskan)* | - | - | - |
@endforelse
@endcomponent

@if($pesanTambahan)
> 📝 **Catatan Administrator:**  
> {{ $pesanTambahan }}
@endif

### 📌 Panduan Singkat untuk Operator:
1. Masuk ke panel operator MONPASKU dengan email dan kata sandi akun Anda.
2. Buka menu **Akses Kamera** di bilah navigasi kiri.
3. Pilih perangkat kamera dari daftar tugas Anda atau masukkan **Kode Akses** di atas, lalu klik **Hubungkan & Buka Scanner**.
4. Scanner siap digunakan untuk memindai kartu PAS pengunjung/petugas di area pos tugas Anda.

@component('mail::button', ['url' => route('operator.kamera.index'), 'color' => 'primary'])
Buka Panel Akses Kamera
@endcomponent

*Penting: Jaga kerahasiaan Kode Akses ini dan hanya gunakan sesuai dengan pos penugasan operasional Anda.*

Terima kasih,<br>
**Administrator Sistem MONPASKU**<br>
Sistem Monitoring PAS Bandara
@endcomponent
