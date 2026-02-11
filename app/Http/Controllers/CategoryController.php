<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Jangan lupa import Str untuk slug

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->query('group');

        // 2. Jika tidak ada group di URL, abort 404 atau redirect
        if (!$group) {
            abort(404);
        }

        // 3. Filter kategori berdasarkan group
        $categories = Category::where('group', $group)
            ->latest()
            ->get();
        return view('admin.categories.index', compact('categories', 'group'));
    }
    public function create(Request $request)
    {
        // 1. Ambil 'group' dari URL (?group=diamart)
        // Jika tidak ada, default ke 'raditya'
        $group = $request->query('group', 'raditya');

        // 2. Kirim variabel $group ke View Create
        return view('admin.categories.create', compact('group'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|unique:categories,category_name',
            // Validasi agar group hanya boleh raditya atau diamart
            'group' => 'required|in:raditya,diamart',
        ]);

        Category::create([
            'category_name' => $request->name,
            // 'slug'          => \Illuminate\Support\Str::slug($request->name),
            'group'         => $request->group, // <--- INI KUNCINYA
        ]);

        // Redirect kembali ke index group yang sesuai
        return redirect()
            ->route('categories.index', ['group' => $request->group])
            ->with('success', 'Kategori berhasil dibuat');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            // Validasi unik, tapi abaikan ID kategori yang sedang diedit
            'name' => 'required|unique:categories,category_name,' . $category->id,
        ]);

        $category->update([
            'category_name' => $request->name,
            // 'slug'          => Str::slug($request->name),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $category)
    {
        $group = $category->group; // Simpan group sebelum dihapus untuk redirect
        $category->delete();

        return redirect()
            ->route('categories.index', ['group' => $group])
            ->with('success', 'Kategori dihapus');
    }

    public function bulkAction(Request $request)
    {
        // 1. Ubah menjadi category_ids agar sesuai dengan name="" di Blade
        $request->validate([
            'category_ids' => 'required|array',
        ]);

        // 2. Ubah juga pemanggilan request-nya di sini
        Category::whereIn('id', $request->category_ids)->delete();

        // 3. (Opsional) Ubah pesannya dari 'Produk' menjadi 'Kategori'
        return redirect()->back()->with('success', 'Kategori terpilih berhasil dihapus.');
    }
}
