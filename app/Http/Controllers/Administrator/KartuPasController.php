<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\KartuPas;
use App\Models\Instansi;
use App\Models\AreaAkses;
use App\Models\Jabatan;
use App\Exports\KartuPasExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class KartuPasController extends Controller
{
    public function index(Request $request)
    {
        // Update status kadaluarsa otomatis
        KartuPas::where('status', 'aktif')
            ->where('tanggal_berlaku', '<', now())
            ->update(['status' => 'kadaluarsa']);

        $query = KartuPas::with('instansi')->latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_pemegang', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_kartu', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('instansi')) {
            $query->where('perusahaan', $request->instansi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kartuPas      = $query->paginate(10)->appends($request->query());
        $instansiList  = Instansi::where('is_active', true)->get();
        $areaAksesList = AreaAkses::orderBy('kode')->get();
        $jabatanList   = Jabatan::orderBy('nama_jabatan')->get();

        return view('administrator.kartu-pas.index', compact('kartuPas', 'instansiList', 'areaAksesList', 'jabatanList'));
    }

    public function tambah()
    {
        $instansiList  = Instansi::where('is_active', true)->get();
        $areaAksesList = AreaAkses::orderBy('kode')->get();
        $jabatanList   = Jabatan::orderBy('nama_jabatan')->get();

        return view('administrator.kartu-pas.tambah', compact('instansiList', 'areaAksesList', 'jabatanList'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'nomor_kartu'     => ['required', 'string', 'unique:kartu_pas', 'max:255'],
            'nama_pemegang'   => ['required', 'string', 'max:255'],
            'instansi_id'     => ['required', 'exists:instansis,id'],
            'area_akses'      => ['required'],
            'jabatan'         => ['nullable', 'string', 'max:255'],
            'email'           => ['nullable', 'email', 'max:255'],
            'tanggal_terbit'  => ['required', 'date'],
            'tanggal_berlaku' => ['required', 'date', 'after:tanggal_terbit'],
        ]);

        $instansi = Instansi::findOrFail($request->instansi_id);

        // Cek Kuota Tersisa
        if ($instansi->sisa_kuota <= 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['instansi_id' => 'Kuota kartu PAS untuk instansi "' . $instansi->nama_instansi . '" sudah habis! (Total kuota: ' . $instansi->kuota . ')']);
        }

        $areaAksesStr = is_array($request->area_akses) ? implode(', ', $request->area_akses) : $request->area_akses;

        KartuPas::create([
            'permohonan_id'   => null,
            'instansi_id'     => $instansi->id,
            'perusahaan'      => $instansi->nama_instansi,
            'nomor_kartu'     => $request->nomor_kartu,
            'email'           => $request->email,
            'nama_pemegang'   => $request->nama_pemegang,
            'area_akses'      => $areaAksesStr,
            'jabatan'         => $request->jabatan,
            'tanggal_terbit'  => $request->tanggal_terbit,
            'tanggal_berlaku' => $request->tanggal_berlaku,
            'status'          => 'aktif',
        ]);

        // Cek dan kirim notifikasi email secara otomatis jika kartu baru langsung mendekati masa kadaluarsa (<30 hari)
        try {
            \Illuminate\Support\Facades\Artisan::call('notifikasi:kadaluarsa');
        } catch (\Throwable $e) {
            // Abaikan error background mail agar proses simpan kartu tidak terganggu
        }

        return redirect()->route('administrator.kartu-pas.index')
            ->with('success', 'Kartu PAS berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $kartuPas      = KartuPas::findOrFail($id);
        $instansiList  = Instansi::where('is_active', true)->get();
        $areaAksesList = AreaAkses::orderBy('kode')->get();
        $jabatanList   = Jabatan::orderBy('nama_jabatan')->get();

        return view('administrator.kartu-pas.edit', compact('kartuPas', 'instansiList', 'areaAksesList', 'jabatanList'));
    }

    public function update(Request $request, int $id)
    {
        $kartuPas = KartuPas::findOrFail($id);

        $request->validate([
            'nomor_kartu'     => ['required', 'string', 'unique:kartu_pas,nomor_kartu,' . $id],
            'nama_pemegang'   => ['required', 'string'],
            'instansi_id'     => ['required', 'exists:instansis,id'],
            'area_akses'      => ['required'],
            'jabatan'         => ['nullable', 'string'],
            'email'           => ['nullable', 'email'],
            'tanggal_terbit'  => ['required', 'date'],
            'tanggal_berlaku' => ['required', 'date', 'after:tanggal_terbit'],
            'status'          => ['required', 'in:aktif,tidak_aktif,kadaluarsa'],
        ]);

        $instansi = Instansi::findOrFail($request->instansi_id);

        // Jika ganti instansi atau status diaktifkan kembali, cek kuota tersisa
        $isChangingInstansi = ($kartuPas->instansi_id != $instansi->id);
        $isActivating       = ($kartuPas->status !== 'aktif' && $request->status === 'aktif');

        if (($isChangingInstansi || $isActivating) && $request->status === 'aktif') {
            if ($instansi->sisa_kuota <= 0) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['instansi_id' => 'Kuota kartu PAS untuk instansi "' . $instansi->nama_instansi . '" sudah habis!']);
            }
        }

        $areaAksesStr = is_array($request->area_akses) ? implode(', ', $request->area_akses) : $request->area_akses;

        $kartuPas->update([
            'nomor_kartu'     => $request->nomor_kartu,
            'nama_pemegang'   => $request->nama_pemegang,
            'email'           => $request->email,
            'instansi_id'     => $instansi->id,
            'perusahaan'      => $instansi->nama_instansi,
            'area_akses'      => $areaAksesStr,
            'jabatan'         => $request->jabatan,
            'tanggal_terbit'  => $request->tanggal_terbit,
            'tanggal_berlaku' => $request->tanggal_berlaku,
            'status'          => $request->status,
        ]);

        // Cek dan kirim notifikasi email secara otomatis jika data kartu diupdate mendekati masa kadaluarsa
        try {
            \Illuminate\Support\Facades\Artisan::call('notifikasi:kadaluarsa');
        } catch (\Throwable $e) {
            // Abaikan error background mail agar proses update kartu tidak terganggu
        }

        return redirect()->route('administrator.kartu-pas.index')
            ->with('success', 'Kartu PAS berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        KartuPas::findOrFail($id)->delete();

        return redirect()->route('administrator.kartu-pas.index')
            ->with('success', 'Kartu PAS berhasil dihapus.');
    }

    public function destroyAll()
    {
        KartuPas::truncate();

        return redirect()->route('administrator.kartu-pas.index')
            ->with('success', 'Semua data kartu PAS berhasil dihapus.');
    }

    public function destroySelected(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        KartuPas::whereIn('id', $request->ids)->delete();

        return redirect()->route('administrator.kartu-pas.index')
            ->with('success', count($request->ids) . ' kartu PAS berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['search', 'instansi', 'status']);
        return Excel::download(new KartuPasExport($filters), 'laporan-kartu-pas-' . date('Ymd') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = KartuPas::latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_pemegang', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_kartu', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('instansi')) {
            $query->where('perusahaan', $request->instansi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kartuPas = $query->get();
        $pdf = Pdf::loadView('administrator.kartu-pas.pdf', compact('kartuPas'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kartu-pas-' . date('Ymd') . '.pdf');
    }

    public function downloadQrCode(int $id)
    {
        $kartu = KartuPas::findOrFail($id);

        $namaPemegang = \Illuminate\Support\Str::slug($kartu->nama_pemegang, '_');
        $instansi     = \Illuminate\Support\Str::slug($kartu->perusahaan, '_');
        $noReg        = \Illuminate\Support\Str::slug($kartu->nomor_kartu, '_');

        $baseFilename = "{$namaPemegang}_{$instansi}_{$noReg}";

        // 1. Coba gunakan chillerlan (GD PNG)
        try {
            if (class_exists('\chillerlan\QRCode\QROptions') && class_exists('\chillerlan\QRCode\QRCode')) {
                $options = new \chillerlan\QRCode\QROptions;
                $options->outputInterface = \chillerlan\QRCode\Output\QRGdImagePNG::class;
                $options->scale = 20;
                $options->imageTransparent = false;
                $options->outputBase64 = false;

                $qr = new \chillerlan\QRCode\QRCode($options);
                $pngData = $qr->render($kartu->nomor_kartu);

                return response($pngData)
                    ->header('Content-Type', 'image/png')
                    ->header('Content-Disposition', 'attachment; filename="' . $baseFilename . '.png"');
            }
        } catch (\Throwable $e) {
            // Lanjut ke fallback jika chillerlan error/tidak kompatibel
        }

        // 2. Fallback universal ke SimpleSoftwareIO QrCode SVG (Tanpa butuh Imagick/GD)
        try {
            $svgData = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(400)
                ->margin(1)
                ->generate($kartu->nomor_kartu);

            return response($svgData)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'attachment; filename="' . $baseFilename . '.svg"');
        } catch (\Throwable $e) {
            // 3. Ultimate Fallback via QR Code API Service
            $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($kartu->nomor_kartu);
            $imageData = @file_get_contents($apiUrl);
            if ($imageData !== false) {
                return response($imageData)
                    ->header('Content-Type', 'image/png')
                    ->header('Content-Disposition', 'attachment; filename="' . $baseFilename . '.png"');
            }

            return redirect()->back()->with('error', 'Gagal mengunduh QR Code.');
        }
    }
}