<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionReportController extends Controller
{
    public function monthlyReport(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $rekap = Transaction::select(
            'users.id',
            'users.name',
            'users.nip',
            'unit_kerja.unit_name',
            DB::raw('COUNT(transactions.id) as total_transaksi')
        )
            ->join('users', 'users.id', '=', 'transactions.id_user')
            ->leftJoin('unit_kerja', 'unit_kerja.id', '=', 'users.id_unit_kerja')
            ->whereMonth('transactions.created_at', $bulan)
            ->whereYear('transactions.created_at', $tahun)
            ->groupBy('users.id', 'users.name', 'users.nip', 'unit_kerja.unit_name')
            ->get();

        return view('admin.reports.monthly', compact('rekap', 'bulan', 'tahun'));
    }

    public function downloadPdf($userId, $bulan, $tahun)
    {
        $user = User::with('unitKerja')->findOrFail($userId);

        $transactions = Transaction::where('id_user', $userId)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at')
            ->get();

        $totalNominal = $transactions->sum('grand_total');

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'user'          => $user,
            'transactions'  => $transactions,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'totalNominal'  => $totalNominal,
        ]);

        return $pdf->stream(
            'invoice_' . $user->nip . '_' . $bulan . '_' . $tahun . '.pdf'
        );
    }
}
