<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use Livewire\Component;

class JournalEntry extends Component
{
    use HasRole;

    public string $transactionType = 'km';
    public string $tanggalJurnal = '';
    public string $nomorBukti = '';
    public string $periodeId = '';

    public array $entries = [
        ['coa_id' => '', 'keterangan' => '', 'debit' => 0, 'kredit' => 0],
    ];

    public $coas;
    public $periodes;

    public function mount()
    {
        $this->coas = COA::orderBy('kode_akun')->get()
            ->map(fn($c) => ['id' => $c->id, 'kode' => $c->kode_akun, 'nama' => $c->nama_akun])
            ->toArray();

        $this->periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        $this->nomorBukti = $this->nextBuktiNumber();
    }

    public function getTotalDebitProperty(): float
    {
        return array_reduce($this->entries, fn($sum, $e) => $sum + (float) ($e['debit'] ?? 0), 0);
    }

    public function getTotalKreditProperty(): float
    {
        return array_reduce($this->entries, fn($sum, $e) => $sum + (float) ($e['kredit'] ?? 0), 0);
    }

    public function getIsBalancedProperty(): bool
    {
        return $this->totalDebit === $this->totalKredit && $this->totalDebit > 0;
    }

    public function getJenisLabelProperty(): string
    {
        return match ($this->transactionType) {
            'km' => 'Kas Masuk',
            'kk' => 'Kas Keluar',
            'bm' => 'Bank Masuk',
            'bk' => 'Bank Keluar',
            'mem' => 'Memorial',
            'mempenutup' => 'Memorial Penutup',
            default => '',
        };
    }

    public function updatedTransactionType()
    {
        $this->nomorBukti = $this->nextBuktiNumber();
    }

    public function addEntry()
    {
        $this->entries[] = ['coa_id' => '', 'keterangan' => '', 'debit' => 0, 'kredit' => 0];
    }

    public function removeEntry(int $index)
    {
        if (count($this->entries) > 1) {
            array_splice($this->entries, $index, 1);
        }
    }

    public function save()
    {
        if (!$this->isBalanced) {
            session()->flash('error', 'Debit dan Kredit harus seimbang.');
            return;
        }

        $this->validate([
            'tanggalJurnal' => 'required|date',
            'nomorBukti' => 'required|string',
            'periodeId' => 'required|exists:periodes,id',
            'entries.*.coa_id' => 'required|exists:coas,id',
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.kredit' => 'required|numeric|min:0',
        ]);

        foreach ($this->entries as $entry) {
            if ($entry['debit'] > 0 || $entry['kredit'] > 0) {
                Jurnaling::create([
                    'tanggal_jurnal' => $this->tanggalJurnal,
                    'nomor_bukti' => $this->nomorBukti,
                    'keterangan' => $entry['keterangan'],
                    'kategori_jurnal' => $this->transactionType,
                    'debit' => $entry['debit'],
                    'kredit' => $entry['kredit'],
                    'coa_id' => $entry['coa_id'],
                    'periode_id' => $this->periodeId,
                ]);
            }
        }

        session()->flash('success', 'Jurnal berhasil disimpan dengan nomor ' . $this->nomorBukti);
        $this->redirect(route('jurnaling'));
    }

    private function nextBuktiNumber(): string
    {
        $prefix = match ($this->transactionType) {
            'km' => 'KM', 'kk' => 'KK', 'bm' => 'BM', 'bk' => 'BK',
            'mem' => 'MEM', 'mempenutup' => 'MEM-PEN', default => 'JV',
        };

        $max = Jurnaling::where('kategori_jurnal', $this->transactionType)
            ->max('nomor_bukti');

        if ($max && preg_match('/(\d+)$/', $max, $m)) {
            return $prefix . '-' . str_pad(((int) $m[1]) + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . '-0001';
    }

    public function render()
    {
        return view('livewire.journal-entry');
    }
}
