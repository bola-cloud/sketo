<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialSummaryExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($revenue, $cogs, $grossProfit, $operatingExpenses, $netProfit, $startDate, $endDate)
    {
        $this->data = collect([
            ['Item' => 'Total Revenue', 'Amount' => $revenue],
            ['Item' => 'Cost of Goods Sold (COGS)', 'Amount' => $cogs],
            ['Item' => 'Gross Profit', 'Amount' => $grossProfit],
            ['Item' => 'Operating Expenses', 'Amount' => $operatingExpenses],
            ['Item' => 'Net Profit', 'Amount' => $netProfit],
        ]);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            ['Income Statement (' . $this->startDate->format('Y-m-d') . ' to ' . $this->endDate->format('Y-m-d') . ')'],
            ['Item', 'Amount']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
