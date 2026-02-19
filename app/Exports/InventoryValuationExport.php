<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryValuationExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Product::where('quantity', '>', 0)->with(['brand', 'category'])->get();
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Category',
            'Brand',
            'Quantity',
            'Cost Price',
            'Selling Price',
            'Total Cost Value',
            'Total Retail Value',
            'Potential Profit',
        ];
    }

    public function map($product): array
    {
        $costValue = $product->quantity * $product->cost_price;
        $retailValue = $product->quantity * $product->selling_price;
        return [
            $product->name,
            $product->category->name ?? 'N/A',
            $product->brand->name ?? 'N/A',
            $product->quantity,
            number_format($product->cost_price, 2),
            number_format($product->selling_price, 2),
            number_format($costValue, 2),
            number_format($retailValue, 2),
            number_format($retailValue - $costValue, 2),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
