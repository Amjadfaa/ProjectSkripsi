<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function pemohon()
    {
        return view('pemohon.dashboard');
    }

    public function administrator()
    {
        return view('administrator.dashboard');
    }

    public function verifikator()
    {
        return view('verifikator.dashboard');
    }
}