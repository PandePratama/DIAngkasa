<?php

namespace App\Http\Controllers;

use App\Models\ProductRaditya;
use App\Models\Category;
use App\Models\Brands;
use Illuminate\Http\Request;

class RadityaController extends Controller
{
    public function index(Request $request)
    {
        // Ambil kategori RADITYA
        $categories = Category::where('group', 'raditya')
            ->orderBy('category_name')
            ->get();

        // Ambil brand
        $brands = Brands::orderBy('brand_name')->get();

        // Ambil produk RADITYA
        $products = ProductRaditya::with(['primaryImage', 'category', 'brand'])
            ->whereHas('category', function ($q) {
                $q->where('group', 'raditya');
            })
            ->when($request->category, function ($q) use ($request) {
                $q->where('id_category', $request->category);
            })
            ->when($request->brand, function ($q) use ($request) {
                $q->where('id_brand', $request->brand);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('gadget.index', compact(
            'products',
            'categories',
            'brands'
        ));
    }

    public function show($id)
    {
        // 1. Ambil Data Produk
        $product = \App\Models\ProductRaditya::with('images')->findOrFail($id);

        // 2. LOGIKA CART LOCK (SATPAM)
        // Cek apakah user sedang punya keranjang Diamart (Sembako)?
        $cartLock = null;
        if (\Illuminate\Support\Facades\Auth::check()) {
            $userCart = \App\Models\Cart::where('id_user', \Illuminate\Support\Facades\Auth::id())->first();

            // Jika ada keranjang, DAN unitnya BUKAN 'raditya' (berarti 'diamart')
            if ($userCart && $userCart->business_unit != 'raditya') {
                $cartLock = 'diamart'; // Kunci halaman ini
            }
        }

        // 3. Kirim ke View
        return view('gadget.show', compact('product', 'cartLock'));
    }
}
