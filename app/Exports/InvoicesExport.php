<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromQuery, WithMapping, WithHeadings, WithStyles
{
    use Exportable;

    public function query()
    {
        return Invoice::query()->with(['client', 'user']);
    }

    public function map($invoice): array
    {
        return [
            $invoice->id,
            $invoice->invoice_code,
            $invoice->client->name ?? 'N/A',
            $invoice->total_amount,
            $invoice->paid_amount,
            $invoice->user->name ?? 'N/A',
            $invoice->created_at->format('Y-m-d H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Invoice Code',
            'Client',
            'Total Amount',
            'Paid Amount',
            'Cashier',
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
