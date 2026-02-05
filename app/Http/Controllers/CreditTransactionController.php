<?php

namespace App\Http\Controllers;

use App\Models\BalanceMutation;
use App\Models\CreditTransaction;
use Illuminate\Http\Request;

class CreditTransactionController extends Controller
{
    public function index(Request $request)
    {
        // -----------------------------------------------------------
        // 1. DATA KREDIT: SEDANG BERJALAN
        // Filter: Ambil yang statusnya murni 'progress'
        // -----------------------------------------------------------
        $creditsOngoing = CreditTransaction::with(['user', 'product', 'installments'])
            ->where('status', 'progress')
            ->latest()
            ->paginate(10, ['*'], 'ongoing_page');

        // -----------------------------------------------------------
        // 2. DATA KREDIT: SUDAH LUNAS
        // Filter: Ambil yang statusnya 'paid' atau 'complete'
        // -----------------------------------------------------------
        $creditsCompleted = CreditTransaction::with(['user', 'product'])
            ->whereIn('status', ['paid', 'complete'])
            ->latest()
            ->paginate(10, ['*'], 'completed_page');

        // -----------------------------------------------------------
        // 3. DATA MUTASI (LOG)
        // -----------------------------------------------------------
        $mutations = BalanceMutation::with('user')
            ->where(function ($query) {
                $query->where('description', 'like', '%Kredit%')
                    ->orWhere('description', 'like', '%Autodebet%')
                    ->orWhere('description', 'like', '%DP%')
                    ->orWhere('description', 'like', '%Angsuran%');
            })
            ->latest()
            ->paginate(15, ['*'], 'mutations_page');

        return view('admin.credits.index', compact(
            'creditsOngoing',
            'creditsCompleted',
            'mutations'
        ));
    }

    // ... method show dan store biarkan tetap sama
}
