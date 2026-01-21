<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Brands;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brands::latest()->get();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands,name',
        ]);

        Brands::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('brands.index')
            ->with('success', 'Brand created successfully');
    }

    public function edit(Brands $brand)
    {
        $brands = Brands::orderBy('name')->get();
        return view('admin.brands.edit', compact('brand', 'brands'));
    }

    public function update(Request $request, Brands $brand)
    {
        $request->validate([
            'name' => 'required|unique:brands,name,' . $brand->id,
        ]);

        $brand->update([
            'name' => $request->name,
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
