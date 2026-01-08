<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'primaryImage'])
            ->latest()
            ->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|unique:products,product_code',
            'name'        => 'required|max:150',
            'specification' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'price_6_months' => 'nullable|numeric',
            'price_9_months' => 'nullable|numeric',
            'price_12_months' => 'nullable|numeric',
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

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_code' => 'required|unique:products,product_code,' . $product->id,
            'name'        => 'required|max:150',
            'specification' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'price_6_months' => 'nullable|numeric',
            'price_9_months' => 'nullable|numeric',
            'price_12_months' => 'nullable|numeric',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($product, $validated, $request) {

            $product->update($validated);

            if ($request->hasFile('images')) {

                ProductImage::where('product_id', $product->id)
                    ->update(['is_primary' => false]);

                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                    ]);
                }
            }
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);

        DB::transaction(function () use ($product) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $product->delete();
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully');
    }

    public function destroyImage($id)
    {
        $image = ProductImage::findOrFail($id);

        DB::transaction(function () use ($image) {

            Storage::disk('public')->delete($image->image_path);

            if ($image->is_primary) {
                ProductImage::where('product_id', $image->product_id)
                    ->where('id', '!=', $image->id)
                    ->first()
                    ?->update(['is_primary' => true]);
            }

            $image->delete();
        });

        return back()->with('success', 'Image deleted successfully');
    }
}
