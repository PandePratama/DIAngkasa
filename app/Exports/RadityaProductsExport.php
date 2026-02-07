<?php

namespace App\Exports;

use App\Models\ProductDiraditya;
use App\Models\ProductRaditya;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RadityaProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return ProductRaditya::with(['category', 'brand'])->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Nama Produk',
            'Kategori',
            'Brand',
            'Stok',
            'Harga',
        ];
    }

    public function map($product): array
    {
        return [
            $product->sku,
            $product->name,
            $product->category->category_name ?? '-',
            $product->brand->brand_name ?? '-',
            $product->stock,
            $product->price,
        ];
    }
}
