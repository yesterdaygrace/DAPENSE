<?php

namespace App\Http\Controllers;

use App\Models\Periode;

class HomeController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function homeRootSuperuser()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'asc')->get();

        return view('rootsuperuser.dashboard', compact('periodes'));
    }

    public function homeOperator()
    {
        return view('operator.dashboard');
    }

    public function homeBod()
    {
        return view('bod.dashboard');
    }
}
