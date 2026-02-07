<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchasesExport implements FromQuery, WithMapping, WithHeadings, WithStyles
{
    use Exportable;

    public function query()
    {
        return Purchase::query()->with('supplier');
    }

    public function map($purchase): array
    {
        return [
            $purchase->id,
            $purchase->invoice_number,
            $purchase->supplier->name ?? 'N/A',
            $purchase->total_amount,
            $purchase->paid_amount,
            $purchase->created_at->format('Y-m-d H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Invoice Number',
            'Supplier',
            'Total Amount',
            'Paid Amount',
            'Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
