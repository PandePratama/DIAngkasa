<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Panggil Model yang baru dibuat (sesuai nama tabel yang dipisah)
use App\Models\ProductDiraditya;
use App\Models\ProductDiamart;
use App\Models\ProductRaditya;

class WelcomeController extends Controller
{
    public function home()
    {
        // -----------------------------------------------------------
        // 1. Section Gadget & Furniture (Raditya)
        // -----------------------------------------------------------
        // Ambil data langsung dari tabel 'product_diraditya'.
        // Tabel ini secara logika bisnis sudah pasti berisi Gadget/Elektronik.
        $productsGadget = ProductRaditya::with(['primaryImage', 'category'])
            ->latest()   // Urutkan dari yang terbaru
            ->limit(4)   // Ambil 4 item
            ->get();

        // -----------------------------------------------------------
        // 2. Section Minimarket (Diamart)
        // -----------------------------------------------------------
        // Ambil data langsung dari tabel 'product_diamart'.
        // Tabel ini secara logika bisnis sudah pasti berisi Sembako/Harian.
        $productsMinimarket = ProductDiamart::with(['primaryImage', 'category'])
            ->latest()
            ->limit(4)
            ->get();

        return view('welcome', compact('productsGadget', 'productsMinimarket'));
    }
}
