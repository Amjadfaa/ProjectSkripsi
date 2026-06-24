@component('mail::message')
# ⚠️ Pemberitahuan Masa Berlaku Kartu PAS

Yth. **{{ $namaInstansi }}**,

Kami memberitahukan bahwa kartu PAS Bandara berikut akan **segera berakhir masa berlakunya**. Harap segera mengurus perpanjangan.

@component('mail::table')
| No. Kartu | Nama Pemegang | Jabatan | Area Akses | Berlaku s/d | Sisa Hari |
|:----------|:--------------|:--------|:-----------|:------------|:---------:|
@foreach($kartuList as $kartu)
@php $sisaHari = now()->diffInDays($kartu->tanggal_berlaku); @endphp
| {{ $kartu->nomor_kartu }} | {{ $kartu->nama_pemegang }} | {{ $kartu->jabatan ?? '-' }} | {{ $kartu->area_akses }} | {{ $kartu->tanggal_berlaku->format('d/m/Y') }} | **{{ $sisaHari }} hari** |
@endforeach
@endcomponent

> 📋 Total **{{ $kartuList->count() }} kartu PAS** dari instansi **{{ $namaInstansi }}** yang akan berakhir dalam 30 hari ke depan.

Segera hubungi Administrator Bandara atau kunjungi sistem MONPASKU untuk melakukan perpanjangan.

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
Kunjungi MONPASKU
@endcomponent

Terima kasih,<br>
**Tim MONPASKU**<br>
Sistem Monitoring PAS Bandara
@endcomponent