<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function home()
    {
        $products = Product::with(['primaryImage', 'category'])
            ->latest()
            ->take(10)
            ->get();

        // Kirim ke view 'welcome' (bukan 'frontend.home')
        return view('welcome', compact('products'));
    }
}
