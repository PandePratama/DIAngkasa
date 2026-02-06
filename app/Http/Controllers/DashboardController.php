<?php

namespace App\Http\Controllers;

use App\Models\ProductDiamart;
use App\Models\ProductRaditya;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalUsers'        => User::count(),
            'totalUnitKerja'    => UnitKerja::count(),
            'totalDiamart'      => ProductDiamart::count(),
            'totalRaditya'      => ProductRaditya::count(),
        ]);
    }
}
