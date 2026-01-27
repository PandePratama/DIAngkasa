<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductDiamart; // PENTING: Pakai Model Diamart
use Illuminate\Http\Request;

class DiamartController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Kategori (Column: category_name)
        // Hanya ambil kategori yang punya produk diamart (opsional, biar rapi)
        $categories = Category::whereHas('productsDiamart')
            ->orderBy('category_name')
            ->get();

        // 2. Ambil Produk Diamart
        $products = ProductDiamart::with(['primaryImage', 'category'])
            ->where('is_active', true) // Jika kolom is_active sudah dibuat
            ->when($request->category, function ($q) use ($request) {
                return $q->where('id_category', $request->category);
            })
            ->latest()
            ->paginate(20);

        // Arahkan ke view di folder 'diamart' (bukan admin)
        return view('diamart.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = ProductDiamart::with(['images', 'category'])
            ->findOrFail($id);

        return view('minimarket.show', compact('product'));
    }
}
