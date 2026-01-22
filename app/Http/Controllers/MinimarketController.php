<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductDiamart; // Gunakan Model Diamart
use Illuminate\Http\Request;

class MinimarketController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Kategori
        // Ganti 'name' menjadi 'category_name'
        // Kita ambil kategori yang memiliki relasi dengan produk diamart saja (opsional),
        // atau ambil semua tapi urutkan berdasarkan category_name.
        $categories = Category::whereHas('productsDiamart')
            ->orderBy('category_name')
            ->get();

        // 2. Ambil Produk Diamart
        // Gunakan Model ProductDiamart (bukan Product biasa)
        $products = ProductDiamart::with(['primaryImage', 'category', 'brand'])
            ->when($request->category, function ($query) use ($request) {
                // Perbaikan: category_id -> id_category
                return $query->where('id_category', $request->category);
            })
            ->when($request->brand, function ($query) use ($request) {
                // Perbaikan: brand_id -> id_brand
                return $query->where('id_brand', $request->brand);
            })
            ->latest()
            ->paginate(20);

        // Pastikan view yang dipanggil sesuai dengan lokasi file view Anda
        // Jika ini halaman admin, gunakan 'admin.diamart.index'
        // Jika ini halaman depan (public), gunakan 'diamart.index'
        return view('minimarket.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        // Ambil detail dari tabel product_diamart
        $product = ProductDiamart::with(['images', 'category', 'brand'])
            ->findOrFail($id);

        // Ambil produk terkait (Related Products)
        $relatedProducts = ProductDiamart::with('primaryImage')
            ->where('id_category', $product->id_category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->inRandomOrder()
            ->get();

        return view('diamart_frontend.show', compact('product', 'relatedProducts'));
        // bisa ubah ke diamart_frontend.show || minimarket.show
    }
}
