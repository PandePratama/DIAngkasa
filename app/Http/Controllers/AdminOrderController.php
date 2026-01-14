<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')
            ->select('orders.*')
            ->selectRaw('SUM(order_items.qty * products.price) as total_belanja')
            ->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->groupBy('orders.id')
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function detailOrder($orderId)
    {
        $order = Order::with(['user', 'items.product'])
            ->findOrFail($orderId);

        return view('admin.orders.detail', compact('order'));
    }

    // public function downloadOrderCsv(Order $order): StreamedResponse
    // {
    //     $filename = 'order_' . $order->id . '.csv';

    //     return response()->streamDownload(function () use ($order) {

    //         $handle = fopen('php://output', 'w');

    //         fputcsv($handle, [
    //             'Order ID',
    //             'User',
    //             'Produk',
    //             'Qty',
    //             'Harga',
    //             'Tipe Pembelian',
    //             'Tenor',
    //             'Subtotal'
    //         ]);

    //         foreach ($order->items as $item) {
    //             fputcsv($handle, [
    //                 $order->id,
    //                 $order->user->name ?? '-',
    //                 $item->product->name,
    //                 $item->qty,
    //                 $item->price,
    //                 strtoupper($item->purchase_type),
    //                 $item->tenor ?? '-',
    //                 $item->qty * $item->price
    //             ]);
    //         }

    //         fclose($handle);
    //     }, $filename, [
    //         'Content-Type' => 'text/csv',
    //     ]);
    // }
}
