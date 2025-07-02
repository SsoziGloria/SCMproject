<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Inventory::query()
            ->with(['product', 'user']);

        if (!empty($this->filters['product'])) {
            $query->where('product_id', $this->filters['product']);
        }

        if (!empty($this->filters['type'])) {
            if ($this->filters['type'] === 'addition') {
                $query->where('quantity', '>', 0);
            } elseif ($this->filters['type'] === 'reduction') {
                $query->where('quantity', '<', 0);
            }
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['batch'])) {
            $query->where('batch_number', 'like', '%' . $this->filters['batch'] . '%');
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Product',
            'Quantity',
            'Type',
            'Batch Number',
            'Manufactured Date',
            'Expiry Date',
            'Reason',
            'Updated By',
            'Date & Time',
        ];
    }

    public function map($inventory): array
    {
        return [
            $inventory->id,
            $inventory->product ? $inventory->product->name : 'Unknown Product',
            abs($inventory->quantity),
            $inventory->quantity > 0 ? 'Addition' : 'Reduction',
            $inventory->batch_number ?? 'N/A',
            $inventory->manufactured_date ? $inventory->manufactured_date->format('Y-m-d') : 'N/A',
            $inventory->expiry_date ? $inventory->expiry_date->format('Y-m-d') : 'N/A',
            $inventory->reason,
            $inventory->user ? $inventory->user->name : 'System',
            $inventory->created_at->format('Y-m-d H:i:s'),
        ];
    }
}