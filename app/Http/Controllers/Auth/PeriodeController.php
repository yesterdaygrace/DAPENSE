<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    /**
     * Show the form to create a new periode.
     */
    public function create()
    {
        return view('auth.create');
    }

    /**
     * Save a new periode and redirect to the login page.
     */
    public function save(Request $request)
    {
        // Validate input
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        // Save the new periode
        Periode::create($request->all());

        // Redirect to login page with success message
        return redirect()->route('login')->with('success', 'Periode created successfully. Please log in.');
    }
}
