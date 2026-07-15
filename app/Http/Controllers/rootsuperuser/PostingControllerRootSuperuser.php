<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Models\Jurnaling;
use App\Models\Periode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostingControllerRootSuperuser
{
    public function index(Request $request)
    {
        // Retrieve all periods for the dropdown
        $periodes = Periode::all();
        $searchTerm = $request->input('search');

        $periodeId = $request->query('periode_id', session('selectedPeriode'));

        if ($periodeId) {
            session(['selectedPeriode' => $periodeId]);
        }

        if ($periodeId) {
            $jurnalings = Jurnaling::with('coa', 'periode')
                ->where('periode_id', $periodeId)
                ->selectRaw('coa_id, SUM(debit) as total_debit, SUM(kredit) as total_kredit')
                ->groupBy('coa_id')
                ->get();
        } else {
            $jurnalings = collect();
        }

        if ($searchTerm) {
            $jurnalings = $jurnalings->filter(function ($entry) use ($searchTerm) {
                return str_contains($entry->coa->nama_akun, $searchTerm) || str_contains($entry->coa->kode_akun, $searchTerm);
            });
        }

        $monthEntries = $jurnalings->groupBy(function ($entry) {
            return Carbon::parse($entry->tanggal_jurnal)->format('n');
        });

        // Define month names
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        // Pass data to the view
        return view('rootsuperuser.posting.home', [
            'periodes' => $periodes,
            'monthEntries' => $monthEntries,
            'months' => $months,
            'periodeId' => $periodeId,
            'selectedPeriode' => Periode::find($periodeId),
            'jurnalings' => $jurnalings,
        ]);
    }

    // Handle journal posting
    public function postJurnal(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $selectedPeriode = Periode::find($periodeId);
        $journalEntries = Jurnaling::where('periode_id', $periodeId)->get();

        foreach ($journalEntries as $entry) {
            $entry->posted = true;
            $entry->save();
        }    // Redirect back to the posting page with the selected period pre-selected

        return redirect()->route('rootsuperuser/posting/post', ['periode_id' => $periodeId])
            ->with('success', 'Journal entries successfully posted!');
    }

    public function getJurnal(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periodes = Periode::all(); // Fetch all periods

        // Fetch the selected period based on periode_id
        $selectedPeriode = Periode::find($periodeId);

        // Retrieve journal entries based on the selected period
        $jurnalings = Jurnaling::where('periode_id', $periodeId)->get();

        // Group journal entries by month
        $monthEntries = $jurnalings->groupBy(function ($entry) {
            return Carbon::parse($entry->tanggal_jurnal)->format('n'); // Group by month number
        });

        // Define month names
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        // Pass data to the view
        return view('rootsuperuser.posting.home', [
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
            'monthEntries' => $monthEntries,
            'months' => $months,
        ]);
    }
}
