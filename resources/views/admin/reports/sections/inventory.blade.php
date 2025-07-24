<div class="sec        <div class="stat-item">
            <span class="stat-value">UGX {{ number_format($data['total_inventory_value'], 0) }}</span>
            <span class="stat-label">Total Inventory Value</span>
        </div>
    <h2>Inventory Overview</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['total_products']) }}</span>
            <span class="stat-label">Total Products</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['low_stock_products']) }}</span>
            <span class="stat-label">Low Stock Items</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['out_of_stock_products']) }}</span>
            <span class="stat-label">Out of Stock</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">UGX {{ number_format($data['total_inventory_value'], 2) }}</span>
            <span class="stat-label">Total Value</span>
        </div>
    </div>
</div>

<div class="section">
    <h2>Product Inventory Details</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Current Stock</th>
                <th>Status</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['products_detail'] as $product)
                <tr>
                    <td>{{ $product['product_name'] }}</td>
                    <td>{{ number_format($product['current_stock']) }}</td>
                    <td>
                        @if($product['status'] === 'Out of Stock')
                            <span class="badge badge-danger">{{ $product['status'] }}</span>
                        @elseif($product['status'] === 'Low Stock')
                            <span class="badge badge-warning">{{ $product['status'] }}</span>
                        @else
                            <span class="badge badge-success">{{ $product['status'] }}</span>
                        @endif
                    </td>
                    <td>UGX {{ number_format($product['value'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h2>Inventory Alerts</h2>
    @if($data['out_of_stock_products'] > 0)
        <p><strong>⚠️ {{ $data['out_of_stock_products'] }} products are out of stock</strong> - Immediate restocking required</p>
    @endif
    
    @if($data['low_stock_products'] > 0)
        <p><strong>⚠️ {{ $data['low_stock_products'] }} products are low on stock</strong> - Consider reordering soon</p>
    @endif
    
    @if($data['out_of_stock_products'] == 0 && $data['low_stock_products'] == 0)
        <p><strong>✅ All products are adequately stocked</strong></p>
    @endif
</div>
