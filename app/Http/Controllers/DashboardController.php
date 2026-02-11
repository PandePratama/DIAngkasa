<?php

namespace App\Http\Controllers;

use App\Models\ProductDiamart;
use App\Models\ProductRaditya;
use App\Models\UnitKerja;
use App\Models\Category; // Tambahkan ini
use App\Models\Brands;   // Tambahkan ini
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalUsers'       => User::count(),
            'totalUnitKerja'   => UnitKerja::count(),
            'totalDiamart'     => ProductDiamart::count(),
            'totalRaditya'     => ProductRaditya::count(),
            'totalCategories'  => Category::count(), // Data baru
            'totalBrands'      => Brands::count(),    // Data baru
        ]);
    }
}
