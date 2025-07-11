<?php
namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;

class ProductsExport implements FromCollection
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::query();

        if (!empty($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
        }
        if (!empty($this->filters['supplier'])) {
            $query->where('supplier_id', $this->filters['supplier']);
        }
        if (!empty($this->filters['stock']) && $this->filters['stock'] === 'low-stock') {
            $query->where('stock', '<=', 10);
        }

        return $query->get();
    }
}