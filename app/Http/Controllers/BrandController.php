<?php

namespace App\Http\Controllers;

// Pastikan Model yang dipakai benar (Singular biasanya lebih standar: Brand)
// Jika nama file model Anda Brands.php, pakai Brands. Jika Brand.php, pakai Brand.
use App\Models\Brands;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        // PERBAIKAN: Gunakan latest() agar data terbaru muncul di atas
        $brands = Brands::latest()->get();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input dari Form (field: name)
        $request->validate([
            'name' => 'required|unique:brands,brand_name',
        ]);

        // 2. Simpan ke Database
        // Kolom DB: brand_name
        // Input Form: name
        Brands::create([
            'brand_name' => $request->name, // PERBAIKAN: Ambil dari $request->name
        ]);

        return redirect()
            ->route('brands.index')
            ->with('success', 'Brand created successfully');
    }

    public function edit(Brands $brand)
    {
        // Tidak perlu query Brand::orderBy... lagi, karena kita hanya edit 1 brand
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brands $brand)
    {
        // Validasi
        $request->validate([
            // unique:table,column,except_id
            'name' => 'required|unique:brands,brand_name,' . $brand->id,
        ]);

        // Update Data
        $brand->update([
            'brand_name' => $request->name, // PERBAIKAN: Kolom DB 'brand_name', input 'name'
        ]);

        return redirect()
            ->route('brands.index')
            ->with('success', 'Brand updated successfully');
    }

    public function destroy(Brands $brand)
    {
        $brand->delete();

        return redirect()
            ->route('brands.index')
            ->with('success', 'Brand deleted successfully');
    }
}