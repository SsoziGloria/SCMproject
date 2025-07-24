<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucwords(str_replace('-', ' ', $report_type)) }} Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #d98323;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #d98323;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 15px;
        }
        .section h2 {
            color: #d98323;
            border-bottom: 2px solid #d98323;
            padding-bottom: 10px;
            margin-top: 0;
            font-size: 16px;
        }
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .metric-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            border: 1px solid #d98323;
            padding: 15px;
            background-color: #fff9f3;
        }
        .metric-value {
            font-size: 18px;
            font-weight: bold;
            color: #d98323;
            display: block;
            margin-bottom: 5px;
        }
        .metric-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #d98323;
            color: white;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .highlight {
            background-color: #fff9f3;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
        }
        .badge-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucwords(str_replace('-', ' ', $report_type)) }} Report</h1>
        <p><strong>Generated:</strong> {{ $generated_at->format('M j, Y \a\t g:i A') }}</p>
        <p><strong>Period:</strong> {{ $date_from->format('M j, Y') }} - {{ $date_to->format('M j, Y') }}</p>
        <p><strong>Retailer ID:</strong> {{ $retailer_id }}</p>
    </div>

    @if($report_type === 'sales' || $report_type === 'comprehensive')
    <div class="section">
        <h2>Sales Performance</h2>
        
        <div class="metrics-grid">
            <div class="metric-item">
                <span class="metric-value">UGX {{ number_format($total_revenue ?? 0, 0) }}</span>
                <span class="metric-label">Total Revenue</span>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ number_format($total_orders ?? 0) }}</span>
                <span class="metric-label">Total Orders</span>
            </div>
            <div class="metric-item">
                <span class="metric-value">UGX {{ number_format($average_order_value ?? 0, 0) }}</span>
                <span class="metric-label">Avg Order Value</span>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ count($top_products ?? []) }}</span>
                <span class="metric-label">Products Sold</span>
            </div>
        </div>

        @if(isset($top_products) && $top_products->count() > 0)
        <h3>Top Selling Products</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th class="text-right">Quantity Sold</th>
                    <th class="text-right">Revenue (UGX)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_products->take(10) as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td class="text-right">{{ number_format($product->total_quantity) }}</td>
                    <td class="text-right">{{ number_format($product->total_revenue, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(isset($daily_sales) && $daily_sales->count() > 0)
        <h3>Daily Sales Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Orders</th>
                    <th class="text-right">Revenue (UGX)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daily_sales as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('M j, Y') }}</td>
                    <td class="text-right">{{ number_format($day->order_count) }}</td>
                    <td class="text-right">{{ number_format($day->revenue, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    @if($report_type === 'inventory' || $report_type === 'comprehensive')
    <div class="section">
        <h2>Inventory Status</h2>
        
        <div class="metrics-grid">
            <div class="metric-item">
                <span class="metric-value">{{ number_format($total_products ?? 0) }}</span>
                <span class="metric-label">Total Products</span>
            </div>
            <div class="metric-item">
                <span class="metric-value">UGX {{ number_format($total_stock_value ?? 0, 0) }}</span>
                <span class="metric-label">Inventory Value</span>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ number_format($low_stock_count ?? 0) }}</span>
                <span class="metric-label">Low Stock Items</span>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ number_format($out_of_stock_count ?? 0) }}</span>
                <span class="metric-label">Out of Stock</span>
            </div>
        </div>

        @if(isset($out_of_stock_items) && $out_of_stock_items->count() > 0)
        <div class="alert alert-danger">
            <strong>Out of Stock Items ({{ $out_of_stock_items->count() }})</strong>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th class="text-right">Current Stock</th>
                    <th class="text-right">Unit Price (UGX)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($out_of_stock_items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right"><span class="badge badge-danger">0</span></td>
                    <td class="text-right">{{ number_format($item->product->price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(isset($low_stock_items) && $low_stock_items->count() > 0)
        <div class="alert alert-warning">
            <strong>Low Stock Items ({{ $low_stock_items->count() }})</strong>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th class="text-right">Current Stock</th>
                    <th class="text-right">Reorder Level</th>
                    <th class="text-right">Unit Price (UGX)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($low_stock_items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right"><span class="badge badge-warning">{{ $item->quantity }}</span></td>
                    <td class="text-right">{{ $item->reorder_level }}</td>
                    <td class="text-right">{{ number_format($item->product->price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(isset($all_inventories) && $all_inventories->count() > 0)
        <h3>Inventory Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th class="text-right">Stock Quantity</th>
                    <th class="text-right">Unit Price (UGX)</th>
                    <th class="text-right">Total Value (UGX)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($all_inventories->take(20) as $inventory)
                <tr class="{{ $inventory->quantity <= $inventory->reorder_level ? 'highlight' : '' }}">
                    <td>{{ $inventory->product->name }}</td>
                    <td class="text-right">{{ number_format($inventory->quantity) }}</td>
                    <td class="text-right">{{ number_format($inventory->product->price, 0) }}</td>
                    <td class="text-right">{{ number_format($inventory->quantity * $inventory->product->price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    @if($report_type === 'customer-summary' || $report_type === 'comprehensive')
    <div class="section">
        <h2>Customer Analytics</h2>
        
        <div class="metrics-grid">
            <div class="metric-item">
                <span class="metric-value">{{ number_format($total_customers ?? 0) }}</span>
                <span class="metric-label">Total Customers</span>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ number_format($new_customers ?? 0) }}</span>
                <span class="metric-label">New Customers</span>
            </div>
            <div class="metric-item">
                <span class="metric-value">{{ isset($top_customers) ? count($top_customers) : 0 }}</span>
                <span class="metric-label">Active Customers</span>
            </div>
            <div class="metric-item">
                <span class="metric-value">UGX {{ isset($top_customers) ? number_format($top_customers->first()['total_spent'] ?? 0, 0) : '0' }}</span>
                <span class="metric-label">Top Customer Spend</span>
            </div>
        </div>

        @if(isset($top_customers) && $top_customers->count() > 0)
        <h3>Top Customers by Spending</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th class="text-right">Orders</th>
                    <th class="text-right">Total Spent (UGX)</th>
                    <th class="text-right">Avg Order Value (UGX)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_customers->take(10) as $customer)
                <tr>
                    <td>{{ $customer['name'] }}</td>
                    <td>{{ $customer['email'] }}</td>
                    <td class="text-right">{{ number_format($customer['order_count']) }}</td>
                    <td class="text-right">{{ number_format($customer['total_spent'], 0) }}</td>
                    <td class="text-right">{{ number_format($customer['average_order_value'], 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the Chocolate SCM System</p>
        <p>Report Date: {{ $generated_at->format('F j, Y \a\t g:i A') }} | Period: {{ $date_from->format('M j, Y') }} - {{ $date_to->format('M j, Y') }}</p>
    </div>
</body>
</html>
