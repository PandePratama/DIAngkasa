<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function home()
    {
        // Gadget categories
        $gadgetCategories = ['Gadget', 'Elektronik', 'Furniture'];

        $productsGadget = Product::with('primaryImage', 'category')
            ->whereHas('category', function ($q) use ($gadgetCategories) {
                $q->whereIn('name', $gadgetCategories);
            })
            ->latest()
            ->limit(4) // ambil 8 best seller
            ->get();

        // Minimarket categories (kecuali gadget)
        $productsMinimarket = Product::with('primaryImage', 'category')
            ->whereHas('category', function ($q) use ($gadgetCategories) {
                $q->whereNotIn('name', $gadgetCategories);
            })
            ->latest()
            ->limit(4) // ambil 8 best seller minimarket
            ->get();

        return view('welcome', compact('productsGadget', 'productsMinimarket'));
    }
}
