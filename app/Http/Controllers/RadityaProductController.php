<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Brands;
use App\Models\Category;
// use App\Models\ProductDiraditya; // PERBAIKAN: Gunakan Model yang benar
use App\Models\ProductImage;
use App\Models\ProductRaditya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RadityaProductController extends Controller
{
    /**
     * Menampilkan daftar produk Gadget/Furniture
     */
    public function index()
    {
        // Ambil data dari tabel product_diraditya
        $products = ProductRaditya::with(['category', 'brand', 'primaryImage'])
            ->latest()
            ->get();

        return view('admin.raditya.index', compact('products'));
    }

    /**
     * Menampilkan form tambah produk
     */
    public function create()
    {
        $categories = Category::orderBy('category_name')->get();
        $brands = Brands::orderBy('brand_name')->get();

        return view('admin.raditya.create', compact('categories', 'brands'));
    }

    /**
     * Menyimpan produk baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validated = $request->validate([
            'sku'           => 'required|unique:product_diraditya,sku',
            'name'          => 'required|max:150',
            'desc'          => 'nullable|string',
            'id_category'   => 'required|exists:categories,id',
            'id_brand'      => 'required|exists:brands,id',
            'stock'         => 'required|integer|min:0',
            'price'         => 'required|numeric|min:0',

            // PERBAIKAN: Input 'warranty_info' kita mapping ke kolom DB 'warranty'
            'warranty_info' => 'nullable|string',

            'images'        => 'required|array|min:1',
            'images.*'      => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 2. Simpan Data Produk
            // Kita harus mapping manual karena nama input beda dengan nama kolom DB
            $product = \App\Models\ProductRaditya::create([
                'sku'         => $validated['sku'],
                'name'        => $validated['name'],
                'desc'        => $validated['desc'],
                'id_category' => $validated['id_category'],
                'id_brand'    => $validated['id_brand'],
                'stock'       => $validated['stock'],
                'price'       => $validated['price'],

                // PENTING: Mapping dari input 'warranty_info' ke kolom DB 'warranty'
                'warranty'    => $validated['warranty_info'] ?? null,
            ]);

            // 3. Simpan Gambar
            foreach ($request->file('images') as $image) {
                $path = $image->store('products/raditya', 'public');

                \App\Models\ProductImage::create([
                    'id_product_diraditya' => $product->id,
                    'image_path'           => $path,
                ]);
            }
        });

        return redirect()
            ->route('raditya.index')
            ->with('success', 'Produk Gadget berhasil ditambahkan');
    }
    /**
     * Menampilkan form edit
     */
    public function edit($id)
    {
        $product = ProductRaditya::with('images')->findOrFail($id);
        $categories = Category::orderBy('category_name')->get();
        $brands = Brands::orderBy('brand_name')->get();

        return view('admin.raditya.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Mengupdate data produk
     */
    public function update(Request $request, $id)
    {
        $product = ProductRaditya::findOrFail($id);

        $validated = $request->validate([
            'sku'           => 'required|unique:product_diraditya,sku,' . $product->id,
            'name'          => 'required|max:150',
            'desc'          => 'nullable|string',
            'id_category'   => 'required|exists:categories,id',
            'id_brand'      => 'required|exists:brands,id',
            'stock'         => 'required|integer|min:0',
            'price'         => 'required|numeric|min:0',
            'warranty_info' => 'nullable|string',
            'images.*'      => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($product, $validated, $request) {
            // 1. Update Data Produk
            $product->update($validated);

            // 2. Tambah Gambar Baru (jika ada)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products/raditya', 'public');

                    ProductImage::create([
                        'id_product_diraditya' => $product->id,
                        'image_path'           => $path,
                    ]);
                }
            }
        });

        return redirect()
            ->route('raditya.index')
            ->with('success', 'Produk Gadget berhasil diperbarui');
    }

    /**
     * Menghapus produk
     */
    public function destroy($id)
    {
        $product = ProductRaditya::with('images')->findOrFail($id);

        DB::transaction(function () use ($product) {
            // Hapus file gambar dari storage
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
            // Hapus produk
            $product->delete();
        });

        return redirect()
            ->route('raditya.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    /**
     * Menghapus satu gambar spesifik (Dipanggil via Route AJAX/Delete)
     */
    public function destroyImage($id)
    {
        $image = ProductImage::findOrFail($id);

        // Pastikan hanya menghapus gambar milik Raditya (Security check)
        if (!$image->id_product_diraditya) {
            return back()->with('error', 'Gambar tidak ditemukan atau bukan milik Raditya');
        }

        DB::transaction(function () use ($image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        });

        return back()->with('success', 'Gambar berhasil dihapus');
    }

    /**
     * Set gambar utama
     */
    public function setPrimaryImage($id)
    {
        // Fitur ini opsional, tergantung apakah di tabel product_images ada kolom 'is_primary'
        // Jika belum ada, Anda bisa menghapus fungsi ini.
        /*
        $image = ProductImage::findOrFail($id);

        DB::transaction(function () use ($image) {
            // Reset semua gambar produk ini jadi false
            ProductImage::where('id_product_diraditya', $image->id_product_diraditya)
                ->update(['is_primary' => false]);

            // Set yang dipilih jadi true
            $image->update(['is_primary' => true]);
        });

        return back()->with('success', 'Thumbnail berhasil diatur');
        */
        return back();
    }
}
