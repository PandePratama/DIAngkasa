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
        // 1. FILTER KATEGORI KHUSUS RADITYA
        // Hanya ambil kategori dengan group 'raditya'
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
            ->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('desc', 'LIKE', '%' . $request->search . '%');
            })
            ->when($request->category, function ($q) use ($request) {
                return $q->where('id_category', $request->category);
            })
            ->when($request->brand, function ($q) use ($request) {
                return $q->where('id_brand', $request->brand);
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

        // 3. RELATED PRODUCTS (RADITYA)
        $product = ProductRaditya::with('primaryImage')->findOrFail($id);

        $relatedProducts = ProductRaditya::with('primaryImage')
            ->where('id', '!=', $product->id)
            ->latest()
            ->limit(10)
            ->get();


        // 3. Kirim ke View
        return view('gadget.show', compact('product', 'cartLock', 'relatedProducts'));
    }


    public function bulkAction(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
        ]);

        ProductRaditya::whereIn('id', $request->product_ids)->delete();

        return redirect()->back()->with('success', 'Produk terpilih berhasil diproses.');
    }
}
