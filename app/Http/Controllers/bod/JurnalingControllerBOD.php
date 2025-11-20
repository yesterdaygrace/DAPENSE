<?php

namespace App\Http\Controllers\bod;

use Illuminate\Http\Request;
use App\Models\Jurnaling;
use App\Models\COA;
use App\Models\NeracaSaldo;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use DateTime;

class JurnalingControllerBOD
{

    public function create()
    {
        return view('bod.jurnaling.create');
    }

    public function save(Request $request)
    {
        $periode = Periode::create($request->all());
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        return redirect()->route('bod/jurnaling', ['periode_id' => $periode->id])
            ->with('successPeriode', 'Periode created successfully.');
    }

    public function index(Request $request)
    {
        $periodes = Periode::where('is_rekap', false)->get();

        $periodeId = $request->query('periode_id', session('selectedPeriode')); // Default to session value if not in request

        // Get all COA and Periode for the dropdowns
        $coas = COA::all();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();

        $jurnalings = [];
        if ($periodeId) {
            $jurnalings = Jurnaling::with('coa', 'periode')
                ->where('periode_id', $periodeId)
                ->get();
        }
        session(['selectedPeriode' => $periodeId]);

        return view('bod.jurnaling.home', [
            'jurnalings' => $jurnalings,
            'coas' => $coas,
            'periodes' => $periodes,
            'sortField' => $request->query('sortField', 'tanggal_jurnal'),
            'sortOrder' => $request->query('sortOrder', 'asc'),
            'selectedPeriode' => $periodeId // Pass the selected period to the view
        ]);
    }

    public function indexkaskeluar(Request $request)
    {
        $periodes = Periode::where('is_rekap', false)->get();

        $periodeId = $request->query('periode_id', session('selectedPeriode')); // Default to session value if not in request

        // Get all COA and Periode for the dropdowns
        $coas = COA::all();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();

        $jurnalings = [];
        if ($periodeId) {
            $jurnalings = Jurnaling::with('coa', 'periode')
                ->where('periode_id', $periodeId)
                ->get();
        }
        session(['selectedPeriode' => $periodeId]);

        return view('bod.jurnaling.kaskeluar', [
            'jurnalings' => $jurnalings,
            'coas' => $coas,
            'periodes' => $periodes,
            'sortField' => $request->query('sortField', 'tanggal_jurnal'),
            'sortOrder' => $request->query('sortOrder', 'asc'),
            'selectedPeriode' => $periodeId // Pass the selected period to the view
        ]);
    }

    public function indexbankmasuk(Request $request)
    {
        $periodes = Periode::where('is_rekap', false)->get();

        $periodeId = $request->query('periode_id', session('selectedPeriode')); // Default to session value if not in request

        // Get all COA and Periode for the dropdowns
        $coas = COA::all();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();

        $jurnalings = [];
        if ($periodeId) {
            $jurnalings = Jurnaling::with('coa', 'periode')
                ->where('periode_id', $periodeId)
                ->get();
        }
        session(['selectedPeriode' => $periodeId]);

        return view('bod.jurnaling.bankmasuk', [
            'jurnalings' => $jurnalings,
            'coas' => $coas,
            'periodes' => $periodes,
            'sortField' => $request->query('sortField', 'tanggal_jurnal'),
            'sortOrder' => $request->query('sortOrder', 'asc'),
            'selectedPeriode' => $periodeId // Pass the selected period to the view
        ]);
    }

    public function indexbankkeluar(Request $request)
    {
        $periodes = Periode::where('is_rekap', false)->get();

        $periodeId = $request->query('periode_id', session('selectedPeriode')); // Default to session value if not in request

        // Get all COA and Periode for the dropdowns
        $coas = COA::all();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();

        $jurnalings = [];
        if ($periodeId) {
            $jurnalings = Jurnaling::with('coa', 'periode')
                ->where('periode_id', $periodeId)
                ->get();
        }
        session(['selectedPeriode' => $periodeId]);

        return view('bod.jurnaling.bankkeluar', [
            'jurnalings' => $jurnalings,
            'coas' => $coas,
            'periodes' => $periodes,
            'sortField' => $request->query('sortField', 'tanggal_jurnal'),
            'sortOrder' => $request->query('sortOrder', 'asc'),
            'selectedPeriode' => $periodeId // Pass the selected period to the view
        ]);
    }

    public function indexmemorial(Request $request)
    {
        $periodes = Periode::where('is_rekap', false)->get();

        $periodeId = $request->query('periode_id', session('selectedPeriode')); // Default to session value if not in request

        // Get all COA and Periode for the dropdowns
        $coas = COA::all();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();

        $jurnalings = [];
        if ($periodeId) {
            $jurnalings = Jurnaling::with('coa', 'periode')
                ->where('periode_id', $periodeId)
                ->get();
        }
        session(['selectedPeriode' => $periodeId]);

        return view('bod.jurnaling.memorial', [
            'jurnalings' => $jurnalings,
            'coas' => $coas,
            'periodes' => $periodes,
            'sortField' => $request->query('sortField', 'tanggal_jurnal'),
            'sortOrder' => $request->query('sortOrder', 'asc'),
            'selectedPeriode' => $periodeId
        ]);
    }

    public function indexmemorialpenutup(Request $request)
    {
        $periodes = Periode::where('is_rekap', false)->get();

        $periodeId = $request->query('periode_id', session('selectedPeriode')); // Default to session value if not in request

        // Get all COA and Periode for the dropdowns
        $coas = COA::all();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();

        $jurnalings = [];
        if ($periodeId) {
            $jurnalings = Jurnaling::with('coa', 'periode')
                ->where('periode_id', $periodeId)
                ->get();
        }
        session(['selectedPeriode' => $periodeId]);

        return view('bod.jurnaling.memorialpenutup', [
            'jurnalings' => $jurnalings,
            'coas' => $coas,
            'periodes' => $periodes,
            'sortField' => $request->query('sortField', 'tanggal_jurnal'),
            'sortOrder' => $request->query('sortOrder', 'asc'),
            'selectedPeriode' => $periodeId
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $periode = Periode::find($request->periode_id);
        $tahunPeriode = \Carbon\Carbon::parse($periode->tanggal_awal)->year;
        $tahunJurnal = \Carbon\Carbon::parse($request->tanggal_jurnal)->year;

        if ($tahunJurnal !== $tahunPeriode) {
            return back()->withErrors([
                'tanggal_jurnal' => 'Tahun jurnal (' . $tahunJurnal . ') tidak sesuai dengan tahun periode (' . $tahunPeriode . ').',
            ])->withInput();
        }

        $nomorBukti = $request->nomor_bukti;

        if (Jurnaling::where('nomor_bukti', $nomorBukti)->exists()) {
            return back()->withErrors(['message' => 'Nomor bukti sudah ada. Silakan gunakan nomor bukti yang lain.']);
        }

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if (bccomp($totalDebit, $totalKredit, 2) !== 0) {
            return back()->withErrors(['message' => 'Total Debit dan Kredit harus seimbang.']);
        }

        $debitEntries = [];
        $kreditEntries = [];

        foreach ($request->coa_id as $index => $coa_id) {
            $entry = [
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $nomorBukti,
                'keterangan' => $request->keterangan[$index] ?? '',
                'coa_id' => $coa_id,
                'debit' => $request->debit[$index] ?? 0,
                'kredit' => $request->kredit[$index] ?? 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ];

            if ($entry['debit'] > 0) {
                $debitEntries[] = $entry;
            } else {
                $kreditEntries[] = $entry;
            }
        }

        $finalEntries = array_merge($debitEntries, $kreditEntries);

        foreach ($finalEntries as $entry) {
            Jurnaling::create($entry);
        }

        return redirect()->route('bod/jurnaling')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diinputkan!',
            ]);
    }


    public function storekaskeluar(Request $request)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $nomorBukti = $request->nomor_bukti;

        if (Jurnaling::where('nomor_bukti', $nomorBukti)->exists()) {
            return back()->withErrors(['message' => 'Nomor bukti sudah ada. Silakan gunakan nomor bukti yang lain.']);
        }

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if (bccomp($totalDebit, $totalKredit, 2) !== 0) {
            return back()->withErrors(['message' => 'Total Debit dan Kredit harus seimbang.']);
        }

        $debitEntries = [];
        $kreditEntries = [];

        foreach ($request->coa_id as $index => $coa_id) {
            $entry = [
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $nomorBukti,
                'keterangan' => $request->keterangan[$index] ?? '',
                'coa_id' => $coa_id,
                'debit' => $request->debit[$index] ?? 0,
                'kredit' => $request->kredit[$index] ?? 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ];

            if ($entry['debit'] > 0) {
                $debitEntries[] = $entry;
            } else {
                $kreditEntries[] = $entry;
            }
        }

        $finalEntries = array_merge($debitEntries, $kreditEntries);

        foreach ($finalEntries as $entry) {
            Jurnaling::create($entry);
        }

        return redirect()->route('bod/jurnaling/kaskeluar')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diinputkan!',
            ]);
    }

    public function storebankmasuk(Request $request)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $periode = Periode::find($request->periode_id);
        $tahunPeriode = \Carbon\Carbon::parse($periode->tanggal_awal)->year;
        $tahunJurnal = \Carbon\Carbon::parse($request->tanggal_jurnal)->year;

        if ($tahunJurnal !== $tahunPeriode) {
            return back()->withErrors([
                'tanggal_jurnal' => 'Tahun jurnal (' . $tahunJurnal . ') tidak sesuai dengan tahun periode (' . $tahunPeriode . ').',
            ])->withInput();
        }

        $nomorBukti = $request->nomor_bukti;

        if (Jurnaling::where('nomor_bukti', $nomorBukti)->exists()) {
            return back()->withErrors(['message' => 'Nomor bukti sudah ada. Silakan gunakan nomor bukti yang lain.']);
        }

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if (bccomp($totalDebit, $totalKredit, 2) !== 0) {
            return back()->withErrors(['message' => 'Total Debit dan Kredit harus seimbang.']);
        }

        $debitEntries = [];
        $kreditEntries = [];

        foreach ($request->coa_id as $index => $coa_id) {
            $entry = [
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $nomorBukti,
                'keterangan' => $request->keterangan[$index] ?? '',
                'coa_id' => $coa_id,
                'debit' => $request->debit[$index] ?? 0,
                'kredit' => $request->kredit[$index] ?? 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ];

            if ($entry['debit'] > 0) {
                $debitEntries[] = $entry;
            } else {
                $kreditEntries[] = $entry;
            }
        }

        $finalEntries = array_merge($debitEntries, $kreditEntries);

        foreach ($finalEntries as $entry) {
            Jurnaling::create($entry);
        }

        return redirect()->route('bod/jurnaling/bankmasuk')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diinputkan!',
            ]);
    }

    public function storebankkeluar(Request $request)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $periode = Periode::find($request->periode_id);
        $tahunPeriode = \Carbon\Carbon::parse($periode->tanggal_awal)->year;
        $tahunJurnal = \Carbon\Carbon::parse($request->tanggal_jurnal)->year;

        if ($tahunJurnal !== $tahunPeriode) {
            return back()->withErrors([
                'tanggal_jurnal' => 'Tahun jurnal (' . $tahunJurnal . ') tidak sesuai dengan tahun periode (' . $tahunPeriode . ').',
            ])->withInput();
        }

        $nomorBukti = $request->nomor_bukti;

        if (Jurnaling::where('nomor_bukti', $nomorBukti)->exists()) {
            return back()->withErrors(['message' => 'Nomor bukti sudah ada. Silakan gunakan nomor bukti yang lain.']);
        }

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if (bccomp($totalDebit, $totalKredit, 2) !== 0) {
            return back()->withErrors(['message' => 'Total Debit dan Kredit harus seimbang.']);
        }

        $debitEntries = [];
        $kreditEntries = [];

        foreach ($request->coa_id as $index => $coa_id) {
            $entry = [
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $nomorBukti,
                'keterangan' => $request->keterangan[$index] ?? '',
                'coa_id' => $coa_id,
                'debit' => $request->debit[$index] ?? 0,
                'kredit' => $request->kredit[$index] ?? 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ];

            if ($entry['debit'] > 0) {
                $debitEntries[] = $entry;
            } else {
                $kreditEntries[] = $entry;
            }
        }

        $finalEntries = array_merge($debitEntries, $kreditEntries);

        foreach ($finalEntries as $entry) {
            Jurnaling::create($entry);
        }

        return redirect()->route('bod/jurnaling/bankkeluar')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diinputkan!',
            ]);
    }

    public function storememorial(Request $request)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'kredit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $periode = Periode::find($request->periode_id);
        $tahunPeriode = \Carbon\Carbon::parse($periode->tanggal_awal)->year;
        $tahunJurnal = \Carbon\Carbon::parse($request->tanggal_jurnal)->year;

        if ($tahunJurnal !== $tahunPeriode) {
            return back()->withErrors([
                'tanggal_jurnal' => 'Tahun jurnal (' . $tahunJurnal . ') tidak sesuai dengan tahun periode (' . $tahunPeriode . ').',
            ])->withInput();
        }

        $nomorBukti = $request->nomor_bukti;
        if (Jurnaling::where('nomor_bukti', $nomorBukti)->exists()) {
            return back()->withErrors(['message' => 'Nomor bukti sudah ada. Silakan gunakan nomor bukti yang lain.']);
        }

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if ($totalDebit !== $totalKredit) {
            return back()->withErrors(['message' => 'Total Debit and Kredit must be balanced.']);
        }
        foreach ($request->coa_id as $index => $coa_id) {
            Jurnaling::create([
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $request->nomor_bukti,
                'keterangan' => $request->keterangan[$index] ?? '',
                'coa_id' => $coa_id,
                'debit' => $request->debit[$index] ?? 0,
                'kredit' => $request->kredit[$index] ?? 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ]);
        }

        return redirect()->route('bod/jurnaling/memorial')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diinputkan!',
            ]);
    }

    public function storememorialpenutup(Request $request)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'kredit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $periode = Periode::find($request->periode_id);
        $tahunPeriode = \Carbon\Carbon::parse($periode->tanggal_awal)->year;
        $tahunJurnal = \Carbon\Carbon::parse($request->tanggal_jurnal)->year;

        if ($tahunJurnal !== $tahunPeriode) {
            return back()->withErrors([
                'tanggal_jurnal' => 'Tahun jurnal (' . $tahunJurnal . ') tidak sesuai dengan tahun periode (' . $tahunPeriode . ').',
            ])->withInput();
        }

        $nomorBukti = $request->nomor_bukti;
        if (Jurnaling::where('nomor_bukti', $nomorBukti)->exists()) {
            return back()->withErrors(['message' => 'Nomor bukti sudah ada. Silakan gunakan nomor bukti yang lain.']);
        }

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if ($totalDebit !== $totalKredit) {
            return back()->withErrors(['message' => 'Total Debit and Kredit must be balanced.']);
        }
        foreach ($request->coa_id as $index => $coa_id) {
            Jurnaling::create([
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $request->nomor_bukti,
                'keterangan' => $request->keterangan[$index] ?? '',
                'coa_id' => $coa_id,
                'debit' => $request->debit[$index] ?? 0,
                'kredit' => $request->kredit[$index] ?? 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ]);
        }

        return redirect()->route('bod/jurnaling/memorialpenutup')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diinputkan!',
            ]);
    }

    public function cekNomorBuktiKM(Request $request)
    {
        $nomorTransaksi = $request->get('nomor_transaksi');
        $tanggalInput = $request->get('tanggal_jurnal');
        if (!$tanggalInput) {
            return response()->json(['error' => 'Tanggal jurnal tidak ditemukan.'], 400);
        }

        $tanggal = DateTime::createFromFormat('Y-m-d', $tanggalInput);
        if (!$tanggal) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $bulan = str_pad($tanggal->format('n'), 2, '0', STR_PAD_LEFT);
        $tahun = substr($tanggal->format('Y'), -2);

        $nomorBukti = 'KM-' . str_pad($nomorTransaksi, 4, '0', STR_PAD_LEFT) . '/' . $bulan . '/' . $tahun;

        $jurnal = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        if ($jurnal->count() > 0) {
            $jurnalDebit = $jurnal->where('debit', '>', 0)->first();
            $jurnalKredit = $jurnal->where('kredit', '>', 0)->first();

            $additionalCoas = [];
            $jurnal->where('id', '!=', $jurnalDebit->id)->where('id', '!=', $jurnalKredit->id)->each(function ($item) use (&$additionalCoas) {
                $additionalCoas[] = [
                    'id' => $item->id,
                    'coa_id' => $item->coa_id,
                    'kode_akun' => $item->coa ? $item->coa->kode_akun : '',
                    'nama_akun' => $item->coa ? $item->coa->nama_akun : '',
                    'kredit' => $item->kredit,
                    'keterangan' => $item->keterangan,
                ];
            });

            return response()->json([
                'exists' => true,
                'id_debit' => $jurnalDebit->id,
                'tanggal_jurnal' => $jurnalDebit->tanggal_jurnal,
                'kategori_jurnal' => $jurnalDebit->kategori_jurnal,
                'keterangan_debit' => $jurnalDebit->keterangan,
                'coa1_id' => $jurnalDebit->coa_id,
                'coa1_kode' => $jurnalDebit->coa ? $jurnalDebit->coa->kode_akun : '',
                'coa1_nama' => $jurnalDebit->coa ? $jurnalDebit->coa->nama_akun : '',
                'debit' => $jurnalDebit->debit,
                'id_kredit' => $jurnalKredit->id,
                'coa2_id' => $jurnalKredit->coa_id,
                'coa2_kode' => $jurnalKredit->coa ? $jurnalKredit->coa->kode_akun : '',
                'coa2_nama' => $jurnalKredit->coa ? $jurnalKredit->coa->nama_akun : '',
                'kredit' => $jurnalKredit->kredit,
                'keterangan_kredit' => $jurnalKredit->keterangan,
                'additional_coas' => $additionalCoas,
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }


    public function cekNomorBuktiKK(Request $request)
    {
        $nomorTransaksi = $request->get('nomor_transaksi');
        $tanggalInput = $request->get('tanggal_jurnal');
        if (!$tanggalInput) {
            return response()->json(['error' => 'Tanggal jurnal tidak ditemukan.'], 400);
        }

        $tanggal = DateTime::createFromFormat('Y-m-d', $tanggalInput);
        if (!$tanggal) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $bulan = str_pad($tanggal->format('n'), 2, '0', STR_PAD_LEFT);
        $tahun = substr($tanggal->format('Y'), -2);

        $nomorBukti = 'KK-' . str_pad($nomorTransaksi, 4, '0', STR_PAD_LEFT) . '/' . $bulan . '/' . $tahun;

        $jurnal = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        if ($jurnal->count() > 0) {
            $jurnalDebit = $jurnal->where('debit', '>', 0)->first();
            $jurnalKredit = $jurnal->where('kredit', '>', 0)->first();

            $additionalCoas = [];
            $jurnal->where('id', '!=', $jurnalDebit->id)->where('id', '!=', $jurnalKredit->id)->each(function ($item) use (&$additionalCoas) {
                $additionalCoas[] = [
                    'id' => $item->id,
                    'coa_id' => $item->coa_id,
                    'kode_akun' => $item->coa ? $item->coa->kode_akun : '',
                    'nama_akun' => $item->coa ? $item->coa->nama_akun : '',
                    'debit' => $item->debit,
                    'keterangan' => $item->keterangan,
                ];
            });

            return response()->json([
                'exists' => true,
                'id_debit' => $jurnalDebit->id,
                'tanggal_jurnal' => $jurnalDebit->tanggal_jurnal,
                'kategori_jurnal' => $jurnalDebit->kategori_jurnal,
                'keterangan_debit' => $jurnalDebit->keterangan,
                'coa1_id' => $jurnalDebit->coa_id,
                'coa1_kode' => $jurnalDebit->coa ? $jurnalDebit->coa->kode_akun : '',
                'coa1_nama' => $jurnalDebit->coa ? $jurnalDebit->coa->nama_akun : '',
                'debit' => $jurnalDebit->debit,
                'id_kredit' => $jurnalKredit->id,
                'coa2_id' => $jurnalKredit->coa_id,
                'coa2_kode' => $jurnalKredit->coa ? $jurnalKredit->coa->kode_akun : '',
                'coa2_nama' => $jurnalKredit->coa ? $jurnalKredit->coa->nama_akun : '',
                'kredit' => $jurnalKredit->kredit,
                // 'keterangan_kredit' => $jurnalKredit->keterangan,
                'additional_coas' => $additionalCoas,
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }


    public function cekNomorBuktiBM(Request $request)
    {
        $nomorTransaksi = str_pad($request->get('nomor_transaksi'), 3, '0', STR_PAD_LEFT);
        $tanggalInput = $request->get('tanggal_jurnal');
        if (!$tanggalInput) {
            return response()->json(['error' => 'Tanggal jurnal tidak ditemukan.'], 400);
        }

        $tanggal = DateTime::createFromFormat('Y-m-d', $tanggalInput);
        if (!$tanggal) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $bulan = str_pad($tanggal->format('n'), 2, '0', STR_PAD_LEFT);
        $tahun = substr($tanggal->format('Y'), -2);

        $nomorAkunAkhir = substr($request->get('nomor_akun'), -4);
        $nomorBukti = $nomorAkunAkhir . '-BM-' . $nomorTransaksi . '/' . $bulan . '/' . $tahun;

        $jurnal = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        if ($jurnal->count() > 0) {
            $jurnalDebit = $jurnal->where('debit', '>', 0)->first();
            $jurnalKredit = $jurnal->where('kredit', '>', 0)->first();

            $additionalCoas = [];
            $jurnal->where('id', '!=', $jurnalDebit->id)->where('id', '!=', $jurnalKredit->id)->each(function ($item) use (&$additionalCoas) {
                $additionalCoas[] = [
                    'id' => $item->id,
                    'coa_id' => $item->coa_id,
                    'kode_akun' => $item->coa ? $item->coa->kode_akun : '',
                    'nama_akun' => $item->coa ? $item->coa->nama_akun : '',
                    'kredit' => $item->kredit,
                    'keterangan' => $item->keterangan,
                ];
            });

            return response()->json([
                'exists' => true,
                'id_debit' => $jurnalDebit->id,
                'tanggal_jurnal' => $jurnalDebit->tanggal_jurnal,
                'kategori_jurnal' => $jurnalDebit->kategori_jurnal,
                'keterangan_debit' => $jurnalDebit->keterangan,
                'coa1_id' => $jurnalDebit->coa_id,
                'coa1_kode' => $jurnalDebit->coa ? $jurnalDebit->coa->kode_akun : '',
                'coa1_nama' => $jurnalDebit->coa ? $jurnalDebit->coa->nama_akun : '',
                'debit' => $jurnalDebit->debit,
                'id_kredit' => $jurnalKredit->id,
                'coa2_id' => $jurnalKredit->coa_id,
                'coa2_kode' => $jurnalKredit->coa ? $jurnalKredit->coa->kode_akun : '',
                'coa2_nama' => $jurnalKredit->coa ? $jurnalKredit->coa->nama_akun : '',
                'kredit' => $jurnalKredit->kredit,
                'keterangan_kredit' => $jurnalKredit->keterangan,
                'additional_coas' => $additionalCoas,
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }

    public function cekNomorBuktiBK(Request $request)
    {
        $nomorTransaksi = str_pad($request->get('nomor_transaksi'), 3, '0', STR_PAD_LEFT);
        $tanggalInput = $request->get('tanggal_jurnal');
        if (!$tanggalInput) {
            return response()->json(['error' => 'Tanggal jurnal tidak ditemukan.'], 400);
        }

        $tanggal = DateTime::createFromFormat('Y-m-d', $tanggalInput);
        if (!$tanggal) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $bulan = str_pad($tanggal->format('n'), 2, '0', STR_PAD_LEFT);
        $tahun = substr($tanggal->format('Y'), -2);

        $nomorAkunAkhir = substr($request->get('nomor_akun'), -4);
        $nomorBukti = $nomorAkunAkhir . '-BK-' . $nomorTransaksi . '/' . $bulan . '/' . $tahun;

        $jurnal = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        if ($jurnal->count() > 0) {
            $jurnalDebit = $jurnal->where('debit', '>', 0)->first();
            $jurnalKredit = $jurnal->where('kredit', '>', 0)->first();


            $additionalCoas = [];
            $jurnal->where('id', '!=', $jurnalDebit->id)->where('id', '!=', $jurnalKredit->id)->each(function ($item) use (&$additionalCoas) {
                $additionalCoas[] = [
                    'id' => $item->id,
                    'coa_id' => $item->coa_id,
                    'kode_akun' => $item->coa ? $item->coa->kode_akun : '',
                    'nama_akun' => $item->coa ? $item->coa->nama_akun : '',
                    'debit' => $item->debit,
                    'keterangan' => $item->keterangan,
                ];
            });

            return response()->json([
                'exists' => true,
                'id_debit' => $jurnalDebit->id,
                'tanggal_jurnal' => $jurnalDebit->tanggal_jurnal,
                'kategori_jurnal' => $jurnalDebit->kategori_jurnal,
                'keterangan_debit' => $jurnalDebit->keterangan,
                'coa1_id' => $jurnalDebit->coa_id,
                'coa1_kode' => $jurnalDebit->coa ? $jurnalDebit->coa->kode_akun : '',
                'coa1_nama' => $jurnalDebit->coa ? $jurnalDebit->coa->nama_akun : '',
                'debit' => $jurnalDebit->debit,
                'id_kredit' => $jurnalKredit->id,
                'coa2_id' => $jurnalKredit->coa_id,
                'coa2_kode' => $jurnalKredit->coa ? $jurnalKredit->coa->kode_akun : '',
                'coa2_nama' => $jurnalKredit->coa ? $jurnalKredit->coa->nama_akun : '',
                'kredit' => $jurnalKredit->kredit,
                'keterangan_kredit' => $jurnalKredit->keterangan,
                'additional_coas' => $additionalCoas,
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }

    public function cekNomorBuktiMem(Request $request)
    {
        $nomorTransaksi = $request->get('nomor_transaksi');
        $tanggalInput = $request->get('tanggal_jurnal');
        if (!$tanggalInput) {
            return response()->json(['error' => 'Tanggal jurnal tidak ditemukan.'], 400);
        }

        $tanggal = DateTime::createFromFormat('Y-m-d', $tanggalInput);
        if (!$tanggal) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $bulan = str_pad($tanggal->format('n'), 2, '0', STR_PAD_LEFT);
        $tahun = substr($tanggal->format('Y'), -2);

        $nomorBukti = 'JM-' . str_pad($nomorTransaksi, 3, '0', STR_PAD_LEFT) . '/' . $bulan . '/' . $tahun;

        $jurnal = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        if ($jurnal->count() > 0) {
            $jurnalDebit = $jurnal->where('debit', '>', 0)->first();
            $jurnalKredit = $jurnal->where('kredit', '>', 0)->first();

            $additionalCoas = [];
            $jurnal->where('id', '!=', $jurnalDebit->id)->where('id', '!=', $jurnalKredit->id)->each(function ($item) use (&$additionalCoas) {
                $additionalCoas[] = [
                    'id' => $item->id,
                    'coa_id' => $item->coa_id,
                    'kode_akun' => $item->coa ? $item->coa->kode_akun : '',
                    'nama_akun' => $item->coa ? $item->coa->nama_akun : '',
                    'debit' => $item->debit,
                    'kredit' => $item->kredit,
                    'keterangan' => $item->keterangan,
                ];
            });

            return response()->json([
                'exists' => true,
                'id_debit' => $jurnalDebit->id,
                'tanggal_jurnal' => $jurnalDebit->tanggal_jurnal,
                'kategori_jurnal' => $jurnalDebit->kategori_jurnal,
                'keterangan_debit' => $jurnalDebit->keterangan,
                'coa1_id' => $jurnalDebit->coa_id,
                'coa1_kode' => $jurnalDebit->coa ? $jurnalDebit->coa->kode_akun : '',
                'coa1_nama' => $jurnalDebit->coa ? $jurnalDebit->coa->nama_akun : '',
                'debit' => $jurnalDebit->debit,
                'id_kredit' => $jurnalKredit->id,
                'coa2_id' => $jurnalKredit->coa_id,
                'coa2_kode' => $jurnalKredit->coa ? $jurnalKredit->coa->kode_akun : '',
                'coa2_nama' => $jurnalKredit->coa ? $jurnalKredit->coa->nama_akun : '',
                'kredit' => $jurnalKredit->kredit,
                'keterangan_kredit' => $jurnalKredit->keterangan,
                'additional_coas' => $additionalCoas,
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }

    public function cekNomorBuktiMemPenutup(Request $request)
    {
        $nomorTransaksi = $request->get('nomor_transaksi');
        $tanggalInput = $request->get('tanggal_jurnal');
        if (!$tanggalInput) {
            return response()->json(['error' => 'Tanggal jurnal tidak ditemukan.'], 400);
        }

        $tanggal = DateTime::createFromFormat('Y-m-d', $tanggalInput);
        if (!$tanggal) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $bulan = str_pad($tanggal->format('n'), 2, '0', STR_PAD_LEFT);
        $tahun = substr($tanggal->format('Y'), -2);

        $nomorBukti = 'JM-' . str_pad($nomorTransaksi, 3, '0', STR_PAD_LEFT) . '/' . $bulan . '/' . $tahun;

        $jurnal = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        if ($jurnal->count() > 0) {
            $jurnalDebit = $jurnal->where('debit', '>', 0)->first();
            $jurnalKredit = $jurnal->where('kredit', '>', 0)->first();

            $additionalCoas = [];
            $jurnal->where('id', '!=', $jurnalDebit->id)->where('id', '!=', $jurnalKredit->id)->each(function ($item) use (&$additionalCoas) {
                $additionalCoas[] = [
                    'id' => $item->id,
                    'coa_id' => $item->coa_id,
                    'kode_akun' => $item->coa ? $item->coa->kode_akun : '',
                    'nama_akun' => $item->coa ? $item->coa->nama_akun : '',
                    'debit' => $item->debit,
                    'kredit' => $item->kredit,
                    'keterangan' => $item->keterangan,
                ];
            });

            return response()->json([
                'exists' => true,
                'id_debit' => $jurnalDebit->id,
                'tanggal_jurnal' => $jurnalDebit->tanggal_jurnal,
                'kategori_jurnal' => $jurnalDebit->kategori_jurnal,
                'keterangan_debit' => $jurnalDebit->keterangan,
                'coa1_id' => $jurnalDebit->coa_id,
                'coa1_kode' => $jurnalDebit->coa ? $jurnalDebit->coa->kode_akun : '',
                'coa1_nama' => $jurnalDebit->coa ? $jurnalDebit->coa->nama_akun : '',
                'debit' => $jurnalDebit->debit,
                'id_kredit' => $jurnalKredit->id,
                'coa2_id' => $jurnalKredit->coa_id,
                'coa2_kode' => $jurnalKredit->coa ? $jurnalKredit->coa->kode_akun : '',
                'coa2_nama' => $jurnalKredit->coa ? $jurnalKredit->coa->nama_akun : '',
                'kredit' => $jurnalKredit->kredit,
                'keterangan_kredit' => $jurnalKredit->keterangan,
                'additional_coas' => $additionalCoas,
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }


    public function updatekm(Request $request, $id)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if (bccomp($totalDebit, $totalKredit, 2) !== 0) {
            return back()->withErrors(['message' => 'Total Debit dan Kredit harus seimbang.']);
        }

        $existingEntries = Jurnaling::where('nomor_bukti', $request->nomor_bukti)->get()->values();

        $debitEntries = [];
        $kreditEntries = [];

        foreach ($request->coa_id as $index => $coa_id) {
            $entry = [
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $request->nomor_bukti,
                'keterangan' => $request->keterangan[$index] ?? '',
                'coa_id' => $coa_id,
                'debit' => is_numeric($request->debit[$index]) ? (float) str_replace(',', '', $request->debit[$index]) : 0,
                'kredit' => is_numeric($request->kredit[$index]) ? (float) str_replace(',', '', $request->kredit[$index]) : 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ];

            if ($entry['debit'] > 0) {
                $debitEntries[] = $entry;
            } else {
                $kreditEntries[] = $entry;
            }
        }

        $finalEntries = array_merge($debitEntries, $kreditEntries);

        foreach ($finalEntries as $i => $entry) {
            if (isset($existingEntries[$i])) {
                $existingEntries[$i]->update($entry);
            } else {
                Jurnaling::create($entry);
            }
        }

        if ($existingEntries->count() > count($finalEntries)) {
            $extraEntries = $existingEntries->slice(count($finalEntries));
            foreach ($extraEntries as $entry) {
                $entry->delete();
            }
        }

        return redirect()->route('bod/jurnaling')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diperbarui!',
            ]);
    }


    public function updatekk(Request $request, $id)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'kredit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if ($totalDebit !== $totalKredit) {
            return back()->withErrors(['message' => 'Total Debit and Kredit must be balanced.']);
        }

        $entries = Jurnaling::where('nomor_bukti', $request->nomor_bukti)->get();

        foreach ($entries as $index => $entry) {
            $entry->update([
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $request->nomor_bukti,
                'keterangan' => $request->keterangan[$index] ?? $entry->keterangan,
                'coa_id' => $request->coa_id[$index] ?? $entry->coa_id,
                'debit' => is_numeric($request->debit[$index]) ? (float) str_replace(',', '', $request->debit[$index]) : 0,
                'kredit' => is_numeric($request->kredit[$index]) ? (float) str_replace(',', '', $request->kredit[$index]) : 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ]);
        }


        return redirect()->route('bod/jurnaling/kaskeluar')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diperbarui!',
            ]);
    }

    public function updatebm(Request $request, $id)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if (bccomp($totalDebit, $totalKredit, 2) !== 0) {
            return back()->withErrors(['message' => 'Total Debit dan Kredit harus seimbang.']);
        }

        $existingEntries = Jurnaling::where('nomor_bukti', $request->nomor_bukti)->get()->values();

        $debitEntries = [];
        $kreditEntries = [];

        foreach ($request->coa_id as $index => $coa_id) {
            $entry = [
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $request->nomor_bukti,
                'keterangan' => $request->keterangan[$index] ?? '',
                'coa_id' => $coa_id,
                'debit' => is_numeric($request->debit[$index]) ? (float) str_replace(',', '', $request->debit[$index]) : 0,
                'kredit' => is_numeric($request->kredit[$index]) ? (float) str_replace(',', '', $request->kredit[$index]) : 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ];

            if ($entry['debit'] > 0) {
                $debitEntries[] = $entry;
            } else {
                $kreditEntries[] = $entry;
            }
        }

        $finalEntries = array_merge($debitEntries, $kreditEntries);

        foreach ($finalEntries as $i => $entry) {
            if (isset($existingEntries[$i])) {
                $existingEntries[$i]->update($entry);
            } else {
                Jurnaling::create($entry);
            }
        }

        if ($existingEntries->count() > count($finalEntries)) {
            $extraEntries = $existingEntries->slice(count($finalEntries));
            foreach ($extraEntries as $entry) {
                $entry->delete();
            }
        }

        return redirect()->route('bod/jurnaling/bankmasuk')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diperbarui!',
            ]);
    }


    public function updatebk(Request $request, $id)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'kredit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if ($totalDebit !== $totalKredit) {
            return back()->withErrors(['message' => 'Total Debit and Kredit must be balanced.']);
        }

        $entries = Jurnaling::where('nomor_bukti', $request->nomor_bukti)->get();

        foreach ($entries as $index => $entry) {
            $entry->update([
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $request->nomor_bukti,
                'keterangan' => $request->keterangan[$index] ?? $entry->keterangan,
                'coa_id' => $request->coa_id[$index] ?? $entry->coa_id,
                'debit' => is_numeric($request->debit[$index]) ? (float) str_replace(',', '', $request->debit[$index]) : 0,
                'kredit' => is_numeric($request->kredit[$index]) ? (float) str_replace(',', '', $request->kredit[$index]) : 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ]);
        }


        return redirect()->route('bod/jurnaling/bankkeluar')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diperbarui!',
            ]);
    }

    public function updatemem(Request $request, $id)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'kredit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $nomorBukti = trim($request->nomor_bukti);

        $entries = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        if ($entries->isEmpty()) {
            return back()->withErrors(['message' => "Nomor bukti {$nomorBukti} tidak ditemukan di database."]);
        }

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if ($totalDebit !== $totalKredit) {
            return back()->withErrors(['message' => 'Total Debit dan Kredit harus seimbang.']);
        }

        // Update data
        foreach ($entries as $index => $entry) {
            $entry->update([
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $nomorBukti, // Gunakan nomor bukti dari form
                'keterangan' => $request->keterangan[$index] ?? $entry->keterangan,
                'coa_id' => $request->coa_id[$index] ?? $entry->coa_id,
                'debit' => is_numeric($request->debit[$index]) ? (float) str_replace(',', '', $request->debit[$index]) : 0,
                'kredit' => is_numeric($request->kredit[$index]) ? (float) str_replace(',', '', $request->kredit[$index]) : 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ]);
        }

        return redirect()->route('bod/jurnaling/memorial')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diperbarui!',
            ]);
    }

    public function updatemempenutup(Request $request, $id)
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'kredit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);

        $nomorBukti = trim($request->nomor_bukti);

        $entries = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        if ($entries->isEmpty()) {
            return back()->withErrors(['message' => "Nomor bukti {$nomorBukti} tidak ditemukan di database."]);
        }

        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if ($totalDebit !== $totalKredit) {
            return back()->withErrors(['message' => 'Total Debit dan Kredit harus seimbang.']);
        }

        // Update data
        foreach ($entries as $index => $entry) {
            $entry->update([
                'tanggal_jurnal' => $request->tanggal_jurnal,
                'nomor_bukti' => $nomorBukti, // Gunakan nomor bukti dari form
                'keterangan' => $request->keterangan[$index] ?? $entry->keterangan,
                'coa_id' => $request->coa_id[$index] ?? $entry->coa_id,
                'debit' => is_numeric($request->debit[$index]) ? (float) str_replace(',', '', $request->debit[$index]) : 0,
                'kredit' => is_numeric($request->kredit[$index]) ? (float) str_replace(',', '', $request->kredit[$index]) : 0,
                'periode_id' => $request->periode_id,
                'kategori_jurnal' => $request->kategori_jurnal,
            ]);
        }

        return redirect()->route('bod/jurnaling/memorialpenutup')
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diperbarui!',
            ]);
    }


    public function showPerMonth(Request $request, $periode = null)
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $selectedPeriode = $periode ?? $request->input('periode_id');

        $months = [];

        if ($selectedPeriode) {
            $periode = Periode::find($selectedPeriode);

            if ($periode) {
                $activeMonths = Jurnaling::where('periode_id', $periode->id)
                    ->selectRaw('DATE_FORMAT(tanggal_jurnal, "%Y-%m") as ym')
                    ->groupBy('ym')
                    ->pluck('ym')
                    ->toArray();

                $startDate = strtotime($periode->tanggal_awal);
                $endDate   = strtotime($periode->tanggal_akhir);

                while ($startDate <= $endDate) {
                    $ym = date('Y-m', $startDate);

                    if (in_array($ym, $activeMonths)) {
                        $months[] = [
                            'id'   => $ym,
                            'name' => date('F Y', $startDate),
                        ];
                    }

                    $startDate = strtotime('+1 month', $startDate);
                }

                $months = array_reverse($months);
            }
        }

        return view('bod/neracasaldo/months', [
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
            'months' => collect($months),
        ]);
    }


    public function showMonths(Request $request, $periode = null)
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $selectedPeriode = $periode ?? $request->input('periode_id');

        $months = [];

        if ($selectedPeriode) {
            $periode = Periode::find($selectedPeriode);

            if ($periode) {
                $activeMonths = Jurnaling::where('periode_id', $periode->id)
                    ->selectRaw('DATE_FORMAT(tanggal_jurnal, "%Y-%m") as ym')
                    ->groupBy('ym')
                    ->pluck('ym')
                    ->toArray();

                $startDate = strtotime($periode->tanggal_awal);
                $endDate   = strtotime($periode->tanggal_akhir);

                while ($startDate <= $endDate) {
                    $ym = date('Y-m', $startDate);

                    if (in_array($ym, $activeMonths)) {
                        $months[] = [
                            'id'   => $ym,
                            'name' => date('F Y', $startDate),
                        ];
                    }

                    $startDate = strtotime('+1 month', $startDate);
                }

                $months = array_reverse($months);
            }
        }

        return view('bod/jurnaling/months', compact('periodes', 'selectedPeriode', 'months'));
    }



    public function rekapJurnalMonth(Request $request, $periode_id)
    {
        $periode = Periode::findOrFail($periode_id);

        if ($periode->is_rekap) {
            return redirect()->back()->with('error', 'Jurnal sudah direkap untuk periode ini.');
        }

        $selectedMonthDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();

        $journalEntries = Jurnaling::where('periode_id', $periode_id)
            ->whereMonth('tanggal_jurnal', $selectedMonthDate->month)
            ->get();

        $balanceByCoa = $journalEntries->groupBy('coa_id')->map(function ($group) {
            return [
                'debit' => $group->sum('debit'),
                'kredit' => $group->sum('kredit'),
            ];
        });

        $allSaldoAwal = SaldoAwal::where('periode_id', $periode_id)
            ->whereDate('tanggal_saldo', $selectedMonthDate->toDateString())
            ->get()
            ->keyBy('coa_id');

        $nextPeriode = null;
        if ($selectedMonthDate->month === 12) {
            $nextYear = $selectedMonthDate->year + 1;
            $nextPeriode = Periode::firstOrCreate([
                'tanggal_awal' => Carbon::create($nextYear, 1, 1)->toDateString(),
                'tanggal_akhir' => Carbon::create($nextYear, 12, 31)->toDateString(),
            ], [
                'nama_periode' => $nextYear,
                'is_rekap' => false,
            ]);
        }

        $neracaSaldoBatch = [];
        $saldoAwalBatch = [];

        COA::chunk(50, function ($coas) use (
            $periode_id,
            $selectedMonthDate,
            $balanceByCoa,
            $nextPeriode,
            $allSaldoAwal,
            &$neracaSaldoBatch,
            &$saldoAwalBatch
        ) {
            foreach ($coas as $coa) {
                if (in_array($coa->kode_akun, ['25000201', '89999999'])) continue;

                $existingSaldoAwal = $allSaldoAwal->get($coa->id);
                $saldoAwalDebit = $existingSaldoAwal?->debit ?? 0;
                $saldoAwalKredit = $existingSaldoAwal?->kredit ?? 0;

                $debit = $balanceByCoa->get($coa->id)['debit'] ?? 0;
                $kredit = $balanceByCoa->get($coa->id)['kredit'] ?? 0;

                $saldoAkhir = ($saldoAwalDebit - $saldoAwalKredit) + ($debit - $kredit);

                $neracaSaldoBatch[] = [
                    'coa_id' => (string) $coa->kode_akun,
                    'periode_id' => $periode_id,
                    'month' => $selectedMonthDate->toDateString(),
                    'debit' => $debit,
                    'kredit' => $kredit,
                    'balance' => $saldoAkhir,
                    'saldo_awal' => $saldoAwalDebit,
                ];

                $nextMonth = $selectedMonthDate->copy()->addMonth()->startOfMonth()->toDateString();

                $saldoAwalBatch[] = [
                    'coa_id' => $coa->id,
                    'periode_id' => $selectedMonthDate->month < 12 ? $periode_id : $nextPeriode->id,
                    'tanggal_saldo' => $selectedMonthDate->month < 12 ? $nextMonth : Carbon::create($selectedMonthDate->year + 1, 1, 1)->toDateString(),
                    'debit' => $saldoAkhir,
                    'kredit' => 0,
                ];
            }
        });

        $totalDebit = collect($neracaSaldoBatch)->whereBetween('coa_id', ['51110001', '82319999'])->sum('debit');
        $totalKredit = collect($neracaSaldoBatch)->whereBetween('coa_id', ['51110001', '82319999'])->sum('kredit');
        $selisih = abs($totalDebit - $totalKredit);
        $isDebitGreater = $totalDebit > $totalKredit;

        $coa25000201 = COA::where('kode_akun', '25000201')->first();
        $coa89999999 = COA::where('kode_akun', '89999999')->first();

        $saldoAwal25000201 = $allSaldoAwal->get($coa25000201->id);
        $saldoAwal89999999 = $allSaldoAwal->get($coa89999999->id);

        $debit25000201 = $balanceByCoa->get($coa25000201->id)['debit'] ?? 0;
        $kredit25000201 = $balanceByCoa->get($coa25000201->id)['kredit'] ?? 0;

        if ($isDebitGreater) {
            $debit25000201 += $selisih;
        } else {
            $kredit25000201 += $selisih;
        }

        $saldoAkhir25000201 = ($saldoAwal25000201?->debit ?? 0 - $saldoAwal25000201?->kredit ?? 0) + ($debit25000201 - $kredit25000201);

        $neracaSaldoBatch[] = [
            'coa_id' => '25000201',
            'periode_id' => $periode_id,
            'month' => $selectedMonthDate->toDateString(),
            'debit' => $debit25000201,
            'kredit' => $kredit25000201,
            'balance' => $saldoAkhir25000201,
            'saldo_awal' => $saldoAwal25000201?->debit ?? 0,
        ];

        $saldoAwalBatch[] = [
            'coa_id' => $coa25000201->id,
            'periode_id' => $selectedMonthDate->month < 12 ? $periode_id : $nextPeriode->id,
            'tanggal_saldo' => $selectedMonthDate->month < 12
                ? $selectedMonthDate->copy()->addMonth()->startOfMonth()->toDateString()
                : Carbon::create($selectedMonthDate->year + 1, 1, 1)->toDateString(),
            'debit' => $saldoAkhir25000201,
            'kredit' => 0,
        ];

        $debit89999999 = !$isDebitGreater ? $selisih : 0;
        $kredit89999999 = $isDebitGreater ? $selisih : 0;

        $saldoAkhir89999999 = ($saldoAwal89999999?->debit ?? 0 - $saldoAwal89999999?->kredit ?? 0) + ($debit89999999 - $kredit89999999);

        $neracaSaldoBatch[] = [
            'coa_id' => '89999999',
            'periode_id' => $periode_id,
            'month' => $selectedMonthDate->toDateString(),
            'debit' => $debit89999999,
            'kredit' => $kredit89999999,
            'balance' => $saldoAkhir89999999,
            'saldo_awal' => $saldoAwal89999999?->debit ?? 0,
        ];

        $saldoAwalBatch[] = [
            'coa_id' => $coa89999999->id,
            'periode_id' => $selectedMonthDate->month < 12 ? $periode_id : $nextPeriode->id,
            'tanggal_saldo' => $selectedMonthDate->month < 12
                ? $selectedMonthDate->copy()->addMonth()->startOfMonth()->toDateString()
                : Carbon::create($selectedMonthDate->year + 1, 1, 1)->toDateString(),
            'debit' => $saldoAkhir89999999,
            'kredit' => 0,
        ];

        $existingNeraca = NeracaSaldo::where('periode_id', $periode_id)
            ->where('month', $selectedMonthDate->toDateString())
            ->get()
            ->keyBy(fn($item) => $item->coa_id . '-' . $item->periode_id . '-' . $item->month);

        $existingSaldoAwal = SaldoAwal::whereIn('tanggal_saldo', [
            $selectedMonthDate->copy()->addMonth()->startOfMonth()->toDateString(),
            Carbon::create($selectedMonthDate->year + 1, 1, 1)->toDateString()
        ])
            ->whereIn('periode_id', [$periode_id, optional($nextPeriode)->id])
            ->get()
            ->keyBy(fn($item) => $item->coa_id . '-' . $item->periode_id . '-' . $item->tanggal_saldo);

        $neracaSaldoBatch = collect($neracaSaldoBatch)->filter(function ($item) use ($existingNeraca) {
            $key = $item['coa_id'] . '-' . $item['periode_id'] . '-' . $item['month'];
            $existing = $existingNeraca->get($key);

            return !$existing || (
                $existing->debit != $item['debit'] ||
                $existing->kredit != $item['kredit'] ||
                $existing->balance != $item['balance'] ||
                $existing->saldo_awal != $item['saldo_awal']
            );
        })->values()->all();

        $saldoAwalBatch = collect($saldoAwalBatch)->filter(function ($item) use ($existingSaldoAwal) {
            $key = $item['coa_id'] . '-' . $item['periode_id'] . '-' . $item['tanggal_saldo'];
            $existing = $existingSaldoAwal->get($key);

            return !$existing || (
                $existing->debit != $item['debit'] ||
                $existing->kredit != $item['kredit']
            );
        })->values()->all();

        NeracaSaldo::upsert(
            $neracaSaldoBatch,
            ['coa_id', 'periode_id', 'month'],
            ['debit', 'kredit', 'balance', 'saldo_awal']
        );

        SaldoAwal::upsert(
            $saldoAwalBatch,
            ['coa_id', 'periode_id', 'tanggal_saldo'],
            ['debit', 'kredit']
        );

        $periode->is_rekap = true;
        $periode->save();

        return redirect()->route('bod/neracasaldo/showing', [
            'periode_id' => $periode_id,
            'month' => $selectedMonthDate->format('Y-m'),
        ])->with('success', 'Neraca Saldo berhasil direkap untuk bulan ini.');
    }



    public function rekapJurnal(Request $request, $periode_id)
    {
        // Fetch the current period
        $periode = Periode::findOrFail($periode_id);

        // Check if the period has already been rekap
        if ($periode->is_rekap) {
            return redirect()->back()->with('error', 'Jurnal sudah direkap untuk periode ini.');
        }

        // Fetch all journal entries for the period
        $journalEntries = Jurnaling::where('periode_id', $periode_id)->get();

        // Group and calculate totals by COA
        $balanceByCoa = $journalEntries->groupBy('coa_id')->map(function ($group) {
            return [
                'debit' => $group->sum('debit'),
                'kredit' => $group->sum('kredit'),
            ];
        });

        // Get all COAs and process saldo_awal and saldo_akhir
        $coas = COA::all();
        foreach ($coas as $coa) {
            // Fetch saldo_awal for the current COA and period
            $existingSaldoAwal = SaldoAwal::where('coa_id', $coa->id)
                ->where('periode_id', $periode_id) // Use the same period
                ->first();

            $saldoAwalDebit = $existingSaldoAwal ? $existingSaldoAwal->debit : 0;
            $saldoAwalKredit = $existingSaldoAwal ? $existingSaldoAwal->kredit : 0;

            $debit = $balanceByCoa->has($coa->id) ? $balanceByCoa[$coa->id]['debit'] : 0;
            $kredit = $balanceByCoa->has($coa->id) ? $balanceByCoa[$coa->id]['kredit'] : 0;

            // Calculate saldoAkhir based on the updated saldo_awal
            $saldoAkhir = ($saldoAwalDebit - $saldoAwalKredit) + ($debit - $kredit);

            // Save or update the NeracaSaldo record
            NeracaSaldo::updateOrCreate(
                [
                    'coa_id' => $coa->id,
                    'periode_id' => $periode_id,
                ],
                [
                    'debit' => $debit,
                    'kredit' => $kredit,
                    'balance' => $saldoAkhir,
                    'saldo_awal' => $saldoAwalDebit, // Save the initial saldo_awal directly
                ]
            );

            // Update saldo_awal for the next period
            $nextPeriode = Periode::where('id', '>', $periode_id)->orderBy('id', 'asc')->first();
            if ($nextPeriode) {
                SaldoAwal::updateOrCreate(
                    [
                        'coa_id' => $coa->id,
                        'periode_id' => $nextPeriode->id,
                    ],
                    [
                        'tanggal_saldo' => now(),
                        'debit' => $saldoAkhir > 0 ? $saldoAkhir : 0,
                        'kredit' => $saldoAkhir < 0 ? abs($saldoAkhir) : 0,
                    ]
                );
            }
        }

        // Mark the period as rekap
        $periode->is_rekap = true;
        $periode->save();

        return redirect()->route('bod/neracasaldo', ['periode_id' => $periode_id])
            ->with('success', 'Jurnal berhasil direkap.');
    }


    public function unrekapJurnal($periode_id)
    {
        $periode = Periode::findOrFail($periode_id);
        $periode->is_rekap = false;
        $periode->save();

        return redirect()->back()->with('success', 'Period has been unrekapped successfully.');
    }
    private function fetchDropdownData()
    {
        return [
            'coas' => COA::all(),
            'periodes' => Periode::all(),
        ];
    }

    public function showJurnaling(Request $request)
    {
        $periodes = Periode::all();
        $coas = COA::all();


        $selectedPeriode = $request->query('periode_id', null);


        $jurnalings = Jurnaling::with('coa')
            ->when($selectedPeriode, function ($query, $selectedPeriode) {
                return $query->where('periode_id', $selectedPeriode);
            })
            ->orderBy('nomor_bukti', 'ASC')
            ->orderBy('tanggal_jurnal', 'asc')
            ->get();


        return view('bod.jurnaling.showing', [
            'periodes' => $periodes,
            'coas' => $coas,
            'jurnalings' => $jurnalings,
            'selectedPeriode' => $selectedPeriode,
        ]);
    }

    public function showEntries(Request $request)
    {
        $month = $request->query('month');
        $periodeId = $request->query('periode_id');


        if (!$month || !$periodeId) {
            return redirect()->route('bod/jurnaling/months')
                ->withErrors(['error' => 'Please select a valid month and period.']);
        }


        $selectedPeriode = Periode::find($periodeId);
        if (!$selectedPeriode) {
            return redirect()->route('bod/jurnaling/months')
                ->withErrors(['error' => 'Invalid period selected.']);
        }


        $year = substr($month, 0, 4);
        $monthNumber = substr($month, 5, 2);


        $monthEntries = Jurnaling::with('coa')
            ->whereYear('tanggal_jurnal', $year)
            ->whereMonth('tanggal_jurnal', $monthNumber)
            ->where('periode_id', $periodeId)
            ->orderBy('tanggal_jurnal', 'asc')
            ->orderBy('nomor_bukti', 'ASC')
            ->get();

        $monthName = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y');

        return view('bod.jurnaling.showing', compact('monthEntries', 'monthName'));
    }
}
