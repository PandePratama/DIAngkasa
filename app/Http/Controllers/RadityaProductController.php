<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductRaditya;
use App\Services\CreditCalculatorService;
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

            // --- TAMBAHAN BARU: Validasi HPP ---
            'hpp'           => 'required|numeric|min:0',
            // -----------------------------------

            'price'         => 'required|numeric|min:0',
            'warranty_info' => 'nullable|string', // Input dari form
            // 'images'        => 'required|array|min:1',
            'images.*'      => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 2. Simpan Data Produk
            $product = ProductRaditya::create([
                'sku'         => $validated['sku'],
                'name'        => $validated['name'],
                'desc'        => $validated['desc'],
                'id_category' => $validated['id_category'],
                'id_brand'    => $validated['id_brand'],
                'stock'       => $validated['stock'],

                // --- TAMBAHAN BARU: Simpan HPP ---
                'hpp'         => $validated['hpp'],
                // ---------------------------------

                'price'       => $validated['price'],

                // Mapping manual: warranty_info (form) -> warranty (db)
                'warranty'    => $validated['warranty_info'] ?? null,
            ]);

            if ($request->hasFile('images')) {
                // 3. Simpan Gambar
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
            'hpp'           => 'required|numeric|min:0',
            'price'         => 'required|numeric|min:0',
            'warranty_info' => 'nullable|string',
            'images.*'      => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($product, $validated, $request) {
            // 1. Update Data Produk (Mapping Manual agar aman)
            $product->update([
                'sku'         => $validated['sku'],
                'name'        => $validated['name'],
                'desc'        => $validated['desc'],
                'id_category' => $validated['id_category'],
                'id_brand'    => $validated['id_brand'],
                'stock'       => $validated['stock'],
                'hpp'         => $validated['hpp'],   // Pastikan HPP terupdate
                'price'       => $validated['price'],
                'warranty'    => $validated['warranty_info'] ?? $product->warranty, // Mapping warranty
            ]);

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
     * Menghapus satu gambar spesifik
     */
    public function destroyImage($id)
    {
        $image = ProductImage::findOrFail($id);

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
     * Simulasi Kredit (API)
     */
    public function simulateCredit(Request $request, CreditCalculatorService $service)
    {
        try {
            $product = ProductRaditya::findOrFail($request->product_id);
            $result = $service->calculate($product, $request->tenor, $request->dp_amount);

            return response()->json([
                'status' => 'success',
                'monthly_base' => number_format($result['monthly_installment']),
                'first_payment' => number_format($result['monthly_installment'] + 20000),
                'admin_fee' => 20000
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function getSimulationSchemes(Request $request, CreditCalculatorService $service)
    {
        $request->validate([
            'product_id' => 'required|exists:product_diraditya,id',
            'dp_amount' => 'nullable|numeric|min:0'
        ]);

        $product = ProductRaditya::findOrFail($request->product_id);
        $dp = $request->dp_amount ?? 0;

        $tenors = [3, 6, 9, 12];
        $schemes = [];

        foreach ($tenors as $tenor) {
            try {
                // Panggil Service kalkulator yang sudah Anda buat sebelumnya
                $calc = $service->calculate($product, $tenor, $dp);

                $schemes[] = [
                    'tenor' => $tenor,
                    'monthly' => number_format($calc['monthly_installment'], 0, ',', '.'),
                    // Total bayar pertama (Angsuran + Admin Fee)
                    'first_payment' => number_format($calc['monthly_installment'] + 20000, 0, ',', '.')
                ];
            } catch (\Exception $e) {
                continue; // Skip jika error (misal DP ketinggian)
            }
        }

        return response()->json([
            'status' => 'success',
            'schemes' => $schemes
        ]);
    }
}
