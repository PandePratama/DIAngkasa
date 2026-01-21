<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $unitKerja = UnitKerja::latest()->get();
        return view('admin.unit-kerja.index', compact('unitKerja'));
    }

    public function create()
    {
        return view('admin.unit-kerja.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_name' => 'required|string|max:100|unique:unit_kerja,unit_name',
        ]);

        UnitKerja::create([
            'unit_name' => $request->unit_name,
        ]);

        return redirect()
            ->route('unit-kerja.index')
            ->with('success', 'Unit kerja berhasil ditambahkan');
    }

    public function edit(UnitKerja $unitKerja)
    {
        return view('admin.unit-kerja.edit', compact('unitKerja'));
    }

    public function update(Request $request, UnitKerja $unitKerja)
    {
        $request->validate([
            'unit_name' => 'required|string|max:100|unique:unit_kerja,unit_name,' . $unitKerja->id,
        ]);

        $unitKerja->update([
            'unit_name' => $request->unit_name,
        ]);

        return redirect()
            ->route('unit-kerja.index')
            ->with('success', 'Unit kerja berhasil diperbarui');
    }

    public function destroy(UnitKerja $unitKerja)
    {
        // Proteksi: tidak bisa hapus jika masih dipakai user
        if ($unitKerja->users()->count() > 0) {
            return back()->with('failed', 'Unit kerja masih digunakan oleh user');
        }

        $unitKerja->delete();

        return redirect()
            ->route('unit-kerja.index')
            ->with('success', 'Unit kerja berhasil dihapus');
    }
}
