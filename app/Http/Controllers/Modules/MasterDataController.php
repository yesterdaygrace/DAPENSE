<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\COA;
use App\Models\HeaderCOA;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index()
    {
        $coaCount = COA::count();
        $headerCoaCount = HeaderCOA::count();
        $periodeCount = Periode::count();
        $saldoAwalCount = SaldoAwal::count();

        return view('modules.master-data.index', compact(
            'coaCount', 
            'headerCoaCount', 
            'periodeCount', 
            'saldoAwalCount'
        ));
    }
}
