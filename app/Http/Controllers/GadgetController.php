<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class GadgetController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        $products = Product::with(['primaryImage', 'category', 'brand'])
            ->when($request->category, function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })
            ->when($request->brand, function ($q) use ($request) {
                $q->where('brand_id', $request->brand);
            })
            ->latest()
            ->paginate(15);

        return view('gadget.index', compact(
            'products',
            'categories',
            'brands'
        ));
    }

    public function show(Product $product)
    {
        $product->load(['images', 'category', 'brand']);

        $relatedProducts = Product::with('primaryImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        return view('gadget.show', compact(
            'product',
            'relatedProducts'
        ));
    }
}
