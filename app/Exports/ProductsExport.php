<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromQuery, WithMapping, WithHeadings, WithStyles
{
    use Exportable;

    public function query()
    {
        return Product::query()->with(['category', 'brand']);
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->category->name ?? 'N/A',
            $product->brand->name ?? 'N/A',
            $product->cost_price,
            $product->selling_price,
            $product->quantity,
            $product->barcode,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Category',
            'Brand',
            'Cost Price',
            'Selling Price',
            'Quantity',
            'Barcode',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
