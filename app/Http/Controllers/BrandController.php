<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Brand;
=======
// Pastikan Model yang dipakai benar (Singular biasanya lebih standar: Brand)
// Jika nama file model Anda Brands.php, pakai Brands. Jika Brand.php, pakai Brand.
>>>>>>> 9760e9aaf315e00c373be4f79539a34c1b0bcd02
use App\Models\Brands;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
<<<<<<< HEAD
=======
        // PERBAIKAN: Gunakan latest() agar data terbaru muncul di atas
>>>>>>> 9760e9aaf315e00c373be4f79539a34c1b0bcd02
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

<<<<<<< HEAD
        Brands::create([
            'name' => $request->name,
=======
        // 2. Simpan ke Database
        // Kolom DB: brand_name
        // Input Form: name
        Brands::create([
            'brand_name' => $request->name, // PERBAIKAN: Ambil dari $request->name
>>>>>>> 9760e9aaf315e00c373be4f79539a34c1b0bcd02
        ]);

        return redirect()
            ->route('brands.index')
            ->with('success', 'Brand created successfully');
    }

    public function edit(Brands $brand)
    {
<<<<<<< HEAD
        $brands = Brands::orderBy('name')->get();
        return view('admin.brands.edit', compact('brand', 'brands'));
=======
        // Tidak perlu query Brand::orderBy... lagi, karena kita hanya edit 1 brand
        return view('admin.brands.edit', compact('brand'));
>>>>>>> 9760e9aaf315e00c373be4f79539a34c1b0bcd02
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