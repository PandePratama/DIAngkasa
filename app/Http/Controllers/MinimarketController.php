<?php

namespace App\Http\Controllers;

use App\Models\Cart; // Jangan lupa import Cart
use App\Models\Category;
use App\Models\ProductDiamart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MinimarketController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Kategori
        $categories = Category::whereHas('productsDiamart')
            ->orderBy('category_name')
            ->get();

        // 2. Ambil Produk Diamart
        $products = ProductDiamart::with(['primaryImage', 'category'])
            ->when($request->category, function ($query) use ($request) {
                return $query->where('id_category', $request->category);
            })
            ->latest()
            ->paginate(20);

        // ============================================================
        // 3. LOGIKA PENGECEKAN KERANJANG (UX PROTECTION)
        // ============================================================
        $cartLock = null; // Default: Tidak terkunci (Boleh belanja)

        if (Auth::check()) {
            $userCart = Cart::where('id_user', Auth::id())->first();

            // Jika user punya keranjang, DAN unitnya BUKAN 'diamart' (berarti 'raditya')
            if ($userCart && $userCart->business_unit != 'diamart') {
                $cartLock = 'raditya'; // Kita kunci halaman ini karena ada keranjang Raditya
            }
        }
        // ============================================================

        return view('minimarket.index', compact('products', 'categories', 'cartLock'));
    }

    public function show($id)
    {
        $product = ProductDiamart::with(['images', 'category'])->findOrFail($id);

        $relatedProducts = ProductDiamart::with('primaryImage')
            ->where('id_category', $product->id_category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->inRandomOrder()
            ->get();

        // ============================================================
        // LOGIKA SAMA UNTUK HALAMAN DETAIL
        // ============================================================
        $cartLock = null;
        if (Auth::check()) {
            $userCart = \App\Models\Cart::where('id_user', Auth::id())->first();
            if ($userCart && $userCart->business_unit != 'diamart') {
                $cartLock = 'raditya';
            }
        }
        // ============================================================

        return view('minimarket.show', compact('product', 'relatedProducts', 'cartLock'));
    }
}
