<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgingReportExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        $receivables = Invoice::with('client')
            ->where('change', '>', 0)
            ->get()
            ->map(function ($invoice) {
                return [
                    'Type' => 'Receivable (Client)',
                    'Name' => $invoice->client->name ?? 'Anonymous',
                    'Reference' => $invoice->invoice_code,
                    'Balance' => $invoice->change,
                    'Date' => $invoice->created_at->format('Y-m-d')
                ];
            });

        $payables = Purchase::with('supplier')
            ->where('change', '>', 0)
            ->get()
            ->map(function ($purchase) {
                return [
                    'Type' => 'Payable (Supplier)',
                    'Name' => $purchase->supplier->name ?? 'Misc Expense',
                    'Reference' => $purchase->invoice_number,
                    'Balance' => $purchase->change,
                    'Date' => $purchase->created_at->format('Y-m-d')
                ];
            });

        return $receivables->concat($payables);
    }

    public function headings(): array
    {
        return [
            'Type',
            'Entity Name',
            'Invoice/Reference',
            'Balance Due',
            'Date Created',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
