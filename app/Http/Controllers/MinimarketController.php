<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MinimarketController extends Controller
{
    public function index(Request $request)
    {
        // Ambil kategori kecuali gadget
        $categories = Category::whereNotIn('name', ['Smartphone', 'Laptop', 'Tablet'])
            ->orderBy('name')
            ->get();

        // Ambil produk minimarket
        $products = Product::with('primaryImage', 'category', 'brand')
            ->whereHas('category', function ($query) {
                $query->whereNotIn('name', ['Smartphone', 'Laptop', 'Tablet']);
            })
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->brand, fn($q) => $q->where('brand_id', $request->brand))
            ->latest()
            ->paginate(20);

        return view('minimarket.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        // Ambil product dengan relasi images, primaryImage, category
        $product = Product::with(['images', 'primaryImage', 'category'])
            ->findOrFail($id);

        // Pastikan product punya category
        if (!$product->category || in_array($product->category->name, ['Smartphone', 'Laptop', 'Tablet'])) {
            abort(403, 'Unauthorized action.');
        }

        // Related products (hanya dari category yang sama)
        $relatedProducts = Product::with('primaryImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        return view('minimarket.show', compact('product', 'relatedProducts'));
    }
}
