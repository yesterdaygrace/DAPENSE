<?php

namespace App\Http\Controllers\Modules;

use App\Exports\COAExport;
use App\Http\Controllers\Controller;
use App\Imports\COAImport;
use App\Models\COA;
use App\Models\HeaderCOA;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class COAWorkspaceController extends Controller
{
    protected function prefix(): string
    {
        return auth()->user()->usertype;
    }

    public function index()
    {
        return $this->accounts();
    }

    public function accounts()
    {
        $coas = COA::with('headerCoa')->paginate(20);
        $headers = HeaderCOA::withCount('coas')->with('parent')->paginate(20);

        return view('modules.master-data.coa-workspace', [
            'activeTab' => 'accounts',
            'coas' => $coas,
            'headers' => $headers,
        ]);
    }

    public function headers()
    {
        $coas = COA::with('headerCoa')->paginate(20);
        $headers = HeaderCOA::withCount('coas')->with('parent')->paginate(20);

        return view('modules.master-data.coa-workspace', [
            'activeTab' => 'headers',
            'coas' => $coas,
            'headers' => $headers,
        ]);
    }

    public function mapping()
    {
        $coas = COA::with('headerCoa')->paginate(20);
        $headers = HeaderCOA::withCount('coas')->with('parent', 'coas')->paginate(20);

        return view('modules.master-data.coa-workspace', [
            'activeTab' => 'mapping',
            'coas' => $coas,
            'headers' => $headers,
        ]);
    }

    public function import()
    {
        $coas = COA::with('headerCoa')->paginate(20);
        $headers = HeaderCOA::withCount('coas')->with('parent')->paginate(20);

        return view('modules.master-data.coa-workspace', [
            'activeTab' => 'import',
            'coas' => $coas,
            'headers' => $headers,
        ]);
    }

    public function export()
    {
        $coas = COA::with('headerCoa')->paginate(20);
        $headers = HeaderCOA::withCount('coas')->with('parent')->paginate(20);

        return view('modules.master-data.coa-workspace', [
            'activeTab' => 'export',
            'coas' => $coas,
            'headers' => $headers,
        ]);
    }

    public function audit()
    {
        $coas = COA::with('headerCoa')->paginate(20);
        $headers = HeaderCOA::withCount('coas')->with('parent')->paginate(20);

        return view('modules.master-data.coa-workspace', [
            'activeTab' => 'audit',
            'coas' => $coas,
            'headers' => $headers,
        ]);
    }

    public function exportData(Request $request)
    {
        $request->validate([
            'format' => 'required|in:excel,csv',
        ]);

        $includeHeaders = $request->boolean('include_headers', true);
        $includeAudit = $request->boolean('include_audit', false);

        $format = $request->input('format', 'csv');
        $extension = $format === 'excel' ? 'xlsx' : 'csv';
        $writerType = $format === 'excel' ? \Maatwebsite\Excel\Excel::XLSX : \Maatwebsite\Excel\Excel::CSV;

        $fileName = 'chart-of-accounts-' . now()->format('Y-m-d') . '.' . $extension;

        return Excel::download(
            new COAExport($includeHeaders, $includeAudit),
            $fileName,
            $writerType
        );
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $file = $request->file('file');

        $import = new COAImport();

        try {
            Excel::import($import, $file);

            $importCount = COA::count();

            return redirect()
                ->route($this->prefix() . '/master-data/coa-workspace', ['tab' => 'import'])
                ->with('success', 'Data COA berhasil diimpor. Total akun: ' . $importCount);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $errorMessages = collect($failures)->map(function ($failure) {
                $header = $failure->attribute();
                $row = $failure->row();
                $errors = implode(', ', $failure->errors());

                return "Baris {$row} - {$header}: {$errors}";
            })->implode('<br>');

            return redirect()
                ->route($this->prefix() . '/master-data/coa-workspace', ['tab' => 'import'])
                ->with('error', 'Gagal mengimpor data:<br>' . $errorMessages);
        } catch (\Exception $e) {
            return redirect()
                ->route($this->prefix() . '/master-data/coa-workspace', ['tab' => 'import'])
                ->with('error', 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = ['kode_akun', 'nama_akun', 'kategori', 'saldo_normal', 'level', 'header'];
        $sample = [
            '100-001',
            'KAS',
            'ASSET',
            'Debit',
            '1',
            'H001',
        ];

        $callback = function () use ($headers, $sample) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $headers);
            fputcsv($file, $sample);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-coa-import.csv"',
        ]);
    }
}
