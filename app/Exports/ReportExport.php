<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Support\Collection;

class ReportExport implements WithMultipleSheets
{
    protected $report;
    protected $data;

    public function __construct($report, $data)
    {
        $this->report = $report;
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Main summary sheet
        $sheets[] = new ReportSummarySheet($this->report, $this->data);

        // Add specific data sheets based on report type
        switch ($this->report->type) {
            case 'sales':
                if (isset($this->data['top_products'])) {
                    $sheets[] = new TopProductsSheet($this->data['top_products']);
                }
                break;
            case 'inventory':
                if (isset($this->data['products_detail'])) {
                    $sheets[] = new InventoryDetailSheet($this->data['products_detail']);
                }
                break;
            case 'comprehensive':
                $sheets[] = new ComprehensiveDataSheet($this->data);
                break;
        }

        return $sheets;
    }
}

class ReportSummarySheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $report;
    protected $data;

    public function __construct($report, $data)
    {
        $this->report = $report;
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();

        // Add report info
        $rows->push(['Report Information', '']);
        $rows->push(['Report Name', $this->report->name]);
        $rows->push(['Report Type', ucfirst($this->report->type)]);
        $rows->push(['Period', $this->report->date_from . ' to ' . $this->report->date_to]);
        $rows->push(['Generated On', now()->format('Y-m-d H:i:s')]);
        $rows->push(['', '']); // Empty row

        // Add summary data based on report type
        switch ($this->report->type) {
            case 'sales':
                $rows->push(['Sales Summary', '']);
                $rows->push(['Total Revenue', '$' . number_format($this->data['total_revenue'] ?? 0, 2)]);
                $rows->push(['Total Orders', number_format($this->data['total_orders'] ?? 0)]);
                $rows->push(['Pending Orders', number_format($this->data['pending_orders'] ?? 0)]);
                $rows->push(['Average Order Value', '$' . number_format($this->data['average_order_value'] ?? 0, 2)]);
                break;

            case 'inventory':
                $rows->push(['Inventory Summary', '']);
                $rows->push(['Total Products', number_format($this->data['total_products'] ?? 0)]);
                $rows->push(['Low Stock Products', number_format($this->data['low_stock_products'] ?? 0)]);
                $rows->push(['Out of Stock Products', number_format($this->data['out_of_stock_products'] ?? 0)]);
                $rows->push(['Total Inventory Value', '$' . number_format($this->data['total_inventory_value'] ?? 0, 2)]);
                break;

            case 'ml-analysis':
                $rows->push(['ML Analysis Summary', '']);
                $segments = $this->data['customer_segments'] ?? [];
                $predictions = $this->data['demand_predictions'] ?? [];
                $rows->push(['Total Customer Segments', $segments['total_segments'] ?? 0]);
                $rows->push(['Total Demand Predictions', $predictions['total_predictions'] ?? 0]);
                $rows->push(['Total Predicted Demand', number_format($predictions['total_predicted_demand'] ?? 0)]);
                break;
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E8F4FD']]
            ],
            'A:B' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
}

class TopProductsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return collect($this->products)->map(function ($product, $index) {
            return [
                $index + 1,
                $product['name'] ?? 'Unknown Product',
                number_format($product['quantity'] ?? 0),
                '$' . number_format($product['revenue'] ?? 0, 2)
            ];
        });
    }

    public function headings(): array
    {
        return ['Rank', 'Product Name', 'Quantity Sold', 'Revenue'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E8F4FD']]
            ]
        ];
    }

    public function title(): string
    {
        return 'Top Products';
    }
}

class InventoryDetailSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return collect($this->products)->map(function ($product) {
            return [
                $product['product_name'] ?? 'Unknown',
                $product['current_stock'] ?? 0,
                $product['status'] ?? 'Unknown',
                '$' . number_format($product['value'] ?? 0, 2)
            ];
        });
    }

    public function headings(): array
    {
        return ['Product Name', 'Current Stock', 'Status', 'Value'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E8F4FD']]
            ]
        ];
    }

    public function title(): string
    {
        return 'Inventory Details';
    }
}

class ComprehensiveDataSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();

        // Add all available data sections
        foreach ($this->data as $section => $sectionData) {
            if (is_array($sectionData)) {
                $rows->push([ucfirst(str_replace('_', ' ', $section)), '']);

                if (isset($sectionData['total_revenue'])) {
                    $rows->push(['Total Revenue', '$' . number_format($sectionData['total_revenue'], 2)]);
                }
                if (isset($sectionData['total_orders'])) {
                    $rows->push(['Total Orders', number_format($sectionData['total_orders'])]);
                }
                if (isset($sectionData['total_products'])) {
                    $rows->push(['Total Products', number_format($sectionData['total_products'])]);
                }

                $rows->push(['', '']); // Empty row between sections
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E8F4FD']]
            ]
        ];
    }

    public function title(): string
    {
        return 'Comprehensive Data';
    }
}
