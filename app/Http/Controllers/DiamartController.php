<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductDiamart; // PENTING: Pakai Model Diamart
use Illuminate\Http\Request;

class DiamartController extends Controller
{
    public function index(Request $request)
    {
        // 1. FILTER KATEGORI KHUSUS DIAMART
        // Kunci mutlak hanya memanggil group 'diamart'
        $categories = Category::where('group', 'diamart')
            ->orderBy('category_name')
            ->get();

        // 2. Ambil Produk Diamart
        $products = ProductDiamart::with(['primaryImage', 'category'])
            // ->where('is_active', true) // Hapus komentar jika fitur ini sudah dipakai
            ->when($request->category, function ($q) use ($request) {
                return $q->where('id_category', $request->category);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString(); // Tambahkan ini agar saat pindah page, filter tidak hilang

        // Arahkan ke view di folder 'diamart' (bukan admin)
        return view('diamart.index', compact('products', 'categories'));
    }
    public function show($id)
    {
        $product = ProductDiamart::with(['images', 'category'])
            ->findOrFail($id);

        return view('minimarket.show', compact('product'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
        ]);

        ProductDiamart::whereIn('id', $request->product_ids)->delete();

        return redirect()->back()->with('success', 'Produk terpilih berhasil diproses.');
    }
}
