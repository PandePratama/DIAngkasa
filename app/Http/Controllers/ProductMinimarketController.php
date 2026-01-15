<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductMinimarketController extends Controller
{
    public function index()
    {
        $products = Product::with('primaryImage', 'category')
            ->whereHas('category', function ($query) {
                $query->whereNotIn('name', ['Gadget', 'Elektronik', 'Furniture']);
            })
            ->latest()
            ->get();
        // dd($products);
        return view('admin.minimarket.index')->with('products', $products);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.minimarket.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|unique:products,product_code',
            'name'        => 'required|max:150',
            'specification' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'images'      => 'required|array|min:1',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {

            $product = Product::create($validated);

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                ]);
            }
        });

        return redirect()->route('minimarket-products.index')
            ->with('success', 'Minimarket product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        return view('admin.minimarket.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_code' => 'required|unique:products,product_code,' . $product->id,
            'name'        => 'required|max:150',
            'specification' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('minimarket-products.index')
            ->with('success', 'Minimarket product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);

        DB::transaction(function () use ($product) {

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            $product->delete();
        });

        return redirect()->route('minimarket-products.index')
            ->with('success', 'Minimarket product deleted successfully.');
    }
}
