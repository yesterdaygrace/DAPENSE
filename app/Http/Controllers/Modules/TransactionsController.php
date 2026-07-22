<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Jurnaling;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index()
    {
        $totalJournals = Jurnaling::count();
        $totalDebit = Jurnaling::sum('debit');
        $totalKredit = Jurnaling::sum('kredit');
        $recentEntries = Jurnaling::orderBy('tanggal_jurnal', 'desc')
            ->take(5)
            ->get();

        return view('modules.transactions.index', compact(
            'totalJournals', 
            'totalDebit', 
            'totalKredit', 
            'recentEntries'
        ));
    }
}
