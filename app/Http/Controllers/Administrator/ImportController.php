<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Imports\KartuPasImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        return view('administrator.import.index');
    }

    public function importKartuPas(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        try {
            $import = new KartuPasImport();
            Excel::import($import, $request->file('file'));

            return redirect()->route('administrator.kartu-pas.index')
                ->with('success', "Import berhasil! {$import->imported} kartu baru, {$import->updated} diupdate, {$import->skipped} dilewati.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}