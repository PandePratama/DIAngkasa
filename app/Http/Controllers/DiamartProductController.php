<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\Category;
use App\Models\ProductDiamart; // Pake Model Diamart
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiamartProductController extends Controller
{
    public function index()
    {
        $products = ProductDiamart::with(['category', 'primaryImage'])->latest()->get();
        return view('admin.diamart.index', compact('products'));
    }

    public function create()
    {
        // FILTER DI SINI:
        // Hanya ambil kategori yang group-nya 'diamart'
        $categories = Category::where('group', 'diamart')
            ->orderBy('category_name')
            ->get();

        return view('admin.diamart.create', compact('categories'));
    }
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'sku'         => 'required|unique:product_diamart,sku',
            'name'        => 'required|max:255',
            'desc'        => 'nullable|string',
            'id_category' => 'required|exists:categories,id',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'images'      => 'required|array|min:1',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {

            // 2. Simpan ke tabel product_diamart
            // Hapus 'sku' karena tidak ada di tabel
            $product = ProductDiamart::create([
                'sku'         => $validated['sku'],
                'name'        => $validated['name'],
                'desc'        => $validated['desc'],
                'id_category' => $validated['id_category'],
                'stock'       => $validated['stock'],
                'price'       => $validated['price'],
            ]);

            // 3. Simpan Gambar ke tabel product_images
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products/diamart', 'public');

                ProductImage::create([
                    'id_product_diamart' => $product->id, // Link ke produk baru
                    'image_path'         => $path,
                    'is_primary'         => $index === 0,
                ]);
            }
        });

        return redirect()
            ->route('diamart.index')
            ->with('success', 'Produk Sembako berhasil ditambahkan');
    }

    // --- EDIT (Menampilkan Form Edit) ---
    public function edit($id)
    {
        // Cari produk berdasarkan ID di tabel product_diamart
        $product = ProductDiamart::with(['images'])->findOrFail($id);

        // Ambil data kategori untuk dropdown
        $categories = Category::where('group', 'diamart')->orderBy('category_name')->get();

        return view('admin.diamart.edit', compact('product', 'categories'));
    }

    // --- UPDATE (Menyimpan Perubahan) ---
    public function update(Request $request, $id)
    {
        $product = ProductDiamart::findOrFail($id);

        // 1. Validasi
        $validated = $request->validate([
            'sku'         => 'required|unique:product_diamart,sku,' . $product->id,
            'name'        => 'required|max:255',
            'desc'        => 'nullable|string',
            'id_category' => 'required|exists:categories,id',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048', // Opsional jika tambah gambar
        ]);

        DB::transaction(function () use ($validated, $request, $product) {

            // 2. Update Data Utama
            $product->update([
                'sku'         => $validated['sku'],
                'name'        => $validated['name'],
                'desc'        => $validated['desc'],
                'id_category' => $validated['id_category'],
                'stock'       => $validated['stock'],
                'price'       => $validated['price'],
            ]);

            // 3. Hapus Gambar Lama (Jika user menghapus dari preview)
            if ($request->has('deleted_images')) {
                $deletedIds = explode(',', $request->deleted_images);
                foreach ($deletedIds as $imgId) {
                    $img = ProductImage::find($imgId);
                    if ($img) {
                        // Hapus file fisik
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
                        // Hapus record database
                        $img->delete();
                    }
                }
            }

            // 4. Tambah Gambar Baru (Jika ada upload baru)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products/diamart', 'public');
                    ProductImage::create([
                        'id_product_diamart' => $product->id,
                        'image_path'         => $path,
                        'is_primary'         => false // Default false, atur manual jika perlu logic primary
                    ]);
                }
            }
        });

        return redirect()->route('diamart.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $product = ProductDiamart::with('images')->findOrFail($id);

        DB::transaction(function () use ($product) {
            // Hapus file gambar dari storage
            foreach ($product->images as $image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
            // Hapus produk
            $product->delete();
        });

        return redirect()
            ->route('diamart.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
