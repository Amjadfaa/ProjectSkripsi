<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;

class PermohonanAdminController extends Controller
{
    public function index()
    {
        $permohonan = Permohonan::with('user')->latest()->get();
        return view('administrator.permohonan.index', compact('permohonan'));
    }

    public function show(int $id)
    {
        $permohonan = Permohonan::with(['user', 'berkas.verifikasi', 'kartuPas'])
            ->findOrFail($id);
        return view('administrator.permohonan.show', compact('permohonan'));
    }
}