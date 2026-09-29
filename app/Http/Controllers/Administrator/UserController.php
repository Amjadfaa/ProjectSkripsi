<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\CameraDevice;
use App\Models\Instansi;
use App\Models\User;
use App\Mail\SendKodeAksesMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna / akun operator dan administrator.
     */
    public function index(Request $request)
    {
        $query = User::with(['cameraDevices.areaAkses']);

        // Pencarian berdasarkan nama, email, atau unit/perusahaan
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('perusahaan', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        // Ringkasan statistik
        $totalUsers          = User::count();
        $totalOperator       = User::where('role', 'operator')->count();
        $totalAdmin          = User::where('role', 'administrator')->count();
        $totalKameraAssigned = \Illuminate\Support\Facades\DB::table('camera_device_user')->distinct('camera_device_id')->count('camera_device_id');

        // Seluruh daftar kamera aktif untuk modal penugasan cepat & tambah akun
        $allCameraDevices = CameraDevice::with('areaAkses')
            ->where('is_active', true)
            ->orderBy('nama_kamera')
            ->get();

        // Daftar instansi aktif untuk autocomplete/select di modal tambah & edit
        $instansiList = Instansi::where('is_active', true)->orWhereNull('is_active')->orderBy('nama_instansi')->get();

        // Respon SPA Partial via AJAX
        if ($request->ajax() || $request->wantsJson() || $request->header('X-SPA')) {
            return view('administrator.users.partials.table', compact(
                'users',
                'totalUsers',
                'totalOperator',
                'totalAdmin',
                'totalKameraAssigned',
                'allCameraDevices'
            ));
        }

        return view('administrator.users.index', compact(
            'users',
            'totalUsers',
            'totalOperator',
            'totalAdmin',
            'totalKameraAssigned',
            'allCameraDevices',
            'instansiList'
        ));
    }

    /**
     * Menampilkan form tambah akun operator / user baru.
     */
    public function create()
    {
        $instansiList = Instansi::where('is_active', true)->orWhereNull('is_active')->orderBy('nama_instansi')->get();
        $cameraDevices = CameraDevice::with('areaAkses')->where('is_active', true)->orderBy('nama_kamera')->get();
        return view('administrator.users.create', compact('instansiList', 'cameraDevices'));
    }

    /**
     * Menyimpan akun operator / user baru ke dalam database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'             => ['required', 'in:administrator,operator'],
            'perusahaan'       => ['nullable', 'string', 'max:255'],
            'password'         => ['required', 'min:8', 'confirmed'],
            'camera_devices'   => ['nullable', 'array'],
            'camera_devices.*' => ['exists:camera_devices,id'],
            'kirim_email'      => ['nullable', 'boolean'],
            'pesan_tambahan'   => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required'     => 'Nama lengkap pengguna wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah terdaftar di sistem. Gunakan email lain.',
            'role.required'     => 'Role akun wajib dipilih.',
            'role.in'           => 'Role hanya boleh Administrator atau Operator.',
            'password.required' => 'Password akun wajib diisi.',
            'password.min'      => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok dengan password.',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'perusahaan' => $request->perusahaan,
            'password'   => Hash::make($request->password),
        ]);

        // Simpan penugasan kamera jika role operator
        $emailTerkirim = false;
        $emailError = null;

        if ($user->role === 'operator' && $request->has('camera_devices')) {
            $user->cameraDevices()->sync($request->input('camera_devices', []));

            if ($request->boolean('kirim_email')) {
                try {
                    $cameras = $user->cameraDevices()->with('areaAkses')->get();
                    Mail::to($user->email)->send(new SendKodeAksesMail($user, $cameras, $request->input('pesan_tambahan')));
                    $emailTerkirim = true;
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email kode akses ke {$user->email}: " . $e->getMessage());
                    $emailError = $e->getMessage();
                }
            }
        }

        $roleLabel = $user->role === 'operator' ? 'Akun Operator' : 'Akun Administrator';
        $message = "{$roleLabel} ({$user->name}) berhasil dibuat!";

        if ($emailTerkirim) {
            $message .= " Rincian kode akses telah dikirimkan ke email ({$user->email}).";
        } elseif ($emailError) {
            $message .= " (Peringatan: Gagal mengirim email: {$emailError})";
        }

        return redirect()->route('administrator.users.index')
            ->with('success', $message);
    }

    /**
     * Menampilkan form edit akun pengguna.
     */
    public function edit(int $id)
    {
        $user = User::with('cameraDevices')->findOrFail($id);
        $instansiList = Instansi::where('is_active', true)->orWhereNull('is_active')->orderBy('nama_instansi')->get();
        $cameraDevices = CameraDevice::with('areaAkses')->where('is_active', true)->orderBy('nama_kamera')->get();
        return view('administrator.users.edit', compact('user', 'instansiList', 'cameraDevices'));
    }

    /**
     * Memperbarui data akun pengguna.
     */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255', 'unique:users,email,' . $id],
            'role'             => ['required', 'in:administrator,operator'],
            'perusahaan'       => ['nullable', 'string', 'max:255'],
            'camera_devices'   => ['nullable', 'array'],
            'camera_devices.*' => ['exists:camera_devices,id'],
            'kirim_email'      => ['nullable', 'boolean'],
            'pesan_tambahan'   => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required'  => 'Nama lengkap pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah digunakan oleh akun lain.',
            'role.required'  => 'Role akun wajib dipilih.',
            'role.in'        => 'Role hanya boleh Administrator atau Operator.',
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'perusahaan' => $request->perusahaan,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['min:8', 'confirmed'],
            ], [
                'password.min'       => 'Password baru minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update penugasan kamera jika role operator
        $emailTerkirim = false;
        $emailError = null;

        if ($user->role === 'operator') {
            $user->cameraDevices()->sync($request->input('camera_devices', []));

            if ($request->boolean('kirim_email')) {
                try {
                    $cameras = $user->cameraDevices()->with('areaAkses')->get();
                    Mail::to($user->email)->send(new SendKodeAksesMail($user, $cameras, $request->input('pesan_tambahan')));
                    $emailTerkirim = true;
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email kode akses ke {$user->email}: " . $e->getMessage());
                    $emailError = $e->getMessage();
                }
            }
        } else {
            // Jika role diubah menjadi administrator, lepaskan penugasan kamera
            $user->cameraDevices()->detach();
        }

        $message = "Data akun {$user->name} berhasil diperbarui.";
        if ($emailTerkirim) {
            $message .= " Rincian kode akses telah dikirimkan ke email ({$user->email}).";
        } elseif ($emailError) {
            $message .= " (Peringatan: Gagal mengirim email: {$emailError})";
        }

        return redirect()->route('administrator.users.index')
            ->with('success', $message);
    }

    /**
     * Menugaskan dan/atau mengirimkan kode akses kamera ke akun operator (dari modal cepat).
     */
    public function assignKamera(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if ($user->role !== 'operator') {
            return redirect()->back()->with('error', 'Penugasan kode akses kamera hanya berlaku untuk akun Operator.');
        }

        $request->validate([
            'camera_ids'     => ['nullable', 'array'],
            'camera_ids.*'   => ['exists:camera_devices,id'],
            'kirim_email'    => ['nullable', 'boolean'],
            'pesan_tambahan' => ['nullable', 'string', 'max:1000'],
        ]);

        $cameraIds = $request->input('camera_ids', []);
        $user->cameraDevices()->sync($cameraIds);

        $emailTerkirim = false;
        $emailError = null;

        if ($request->boolean('kirim_email')) {
            try {
                $cameras = $user->cameraDevices()->with('areaAkses')->get();
                Mail::to($user->email)->send(new SendKodeAksesMail($user, $cameras, $request->input('pesan_tambahan')));
                $emailTerkirim = true;
            } catch (\Exception $e) {
                Log::error("Gagal mengirim email kode akses ke {$user->email}: " . $e->getMessage());
                $emailError = $e->getMessage();
            }
        }

        $count = count($cameraIds);
        $message = "Berhasil memperbarui penugasan ({$count} perangkat kamera) untuk Operator {$user->name}.";

        if ($emailTerkirim) {
            $message .= " Rincian kode akses juga telah berhasil dikirimkan ke email ({$user->email})!";
        } elseif ($emailError) {
            $message .= " (Namun email gagal terkirim: {$emailError})";
        }

        return redirect()->route('administrator.users.index')
            ->with('success', $message);
    }

    /**
     * Menghapus akun pengguna dari sistem.
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('administrator.users.index')
            ->with('success', "Akun ({$nama}) berhasil dihapus dari sistem.");
    }
}