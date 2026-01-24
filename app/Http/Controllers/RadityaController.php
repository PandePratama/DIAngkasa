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

    public function show(ProductRaditya $product)
    {
        $product->load(['images', 'category', 'brand']);

        $relatedProducts = ProductRaditya::with('primaryImage')
            ->where('id_category', $product->id_category)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        return view('gadget.show', compact(
            'product',
            'relatedProducts'
        ));
    }
}
