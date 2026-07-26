<?php

namespace App\Http\Controllers\Base;

use App\Models\Otorisator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Facades\Activity;

class OtorisatorController
{
    protected function viewPrefix(): string
    {
        return Auth::user()->usertype;
    }

    protected function routePrefix(): string
    {
        return Auth::user()->usertype;
    }

    public function index()
    {
        $otorisators = Otorisator::orderBy('id', 'asc')->get();

        return view($this->viewPrefix() . '.otorisator.home', compact('otorisators'));
    }

    /**
     * Form tambah otorisator
     */
    public function create()
    {
        return view($this->viewPrefix() . '.otorisator.create');
    }

    /**
     * Simpan otorisator baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_otorisator' => 'required|string|max:255',
            'jabatan_otorisator' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            Otorisator::create($request->only(['nama_otorisator', 'jabatan_otorisator']));
        });

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['nama' => $request->nama_otorisator])
            ->log('Otorisator ' . $request->nama_otorisator . ' ditambahkan');

        return redirect()->route($this->routePrefix() . '/otorisator/home')
            ->with('success', 'Otorisator berhasil ditambahkan.');
    }

    /**
     * Form edit otorisator
     */
    public function edit($id)
    {
        $otorisator = Otorisator::findOrFail($id);
        Gate::authorize('update', $otorisator);

        return view($this->viewPrefix() . '.otorisator.update', compact('otorisator'));
    }

    /**
     * Update otorisator
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_otorisator' => 'required|string|max:255',
            'jabatan_otorisator' => 'required|string|max:255',
        ]);

        Gate::authorize('update', Otorisator::findOrFail($id));

        DB::transaction(function () use ($request, $id) {
            $otorisator = Otorisator::findOrFail($id);
            $otorisator->update($request->only(['nama_otorisator', 'jabatan_otorisator']));
        });

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['otorisator_id' => $id])
            ->log('Otorisator ID ' . $id . ' diperbarui');

        return redirect()->route($this->routePrefix() . '/otorisator/home')
            ->with('success', 'Otorisator berhasil diperbarui.');
    }

    /**
     * Hapus otorisator
     */
    public function destroy($id)
    {
        Gate::authorize('delete', Otorisator::findOrFail($id));

        DB::transaction(function () use ($id) {
            $otorisator = Otorisator::findOrFail($id);
            $otorisator->delete();
        });

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['otorisator_id' => $id])
            ->log('Otorisator ID ' . $id . ' dihapus');

        return redirect()->route($this->routePrefix() . '/otorisator/home')
            ->with('success', 'Otorisator berhasil dihapus.');
    }
}
