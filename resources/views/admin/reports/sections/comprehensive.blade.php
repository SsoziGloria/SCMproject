<div class="section">
    <h2>Executive Summary</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-value">UGX {{ number_format($data['executive_summary']['total_revenue'], 0) }}</span>
            <span class="stat-label">Total Revenue</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['executive_summary']['total_orders']) }}</span>
            <span class="stat-label">Total Orders</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">UGX {{ number_format($data['executive_summary']['inventory_value'], 0) }}</span>
            <span class="stat-label">Inventory Value</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $data['executive_summary']['ml_insights_available'] ? 'Available' : 'Pending' }}</span>
            <span class="stat-label">ML Insights</span>
        </div>
    </div>
</div>

<!-- Sales Section -->
<div class="section">
    <h2>Sales Performance</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-value">UGX {{ number_format($data['sales']['total_revenue'], 0) }}</span>
            <span class="stat-label">Revenue</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['sales']['total_orders']) }}</span>
            <span class="stat-label">Orders</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['sales']['pending_orders']) }}</span>
            <span class="stat-label">Pending</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">UGX {{ number_format($data['sales']['average_order_value'], 0) }}</span>
            <span class="stat-label">Avg Order</span>
        </div>
    </div>
</div>

<!-- Inventory Section -->
<div class="section">
    <h2>Inventory Status</h2>
    <table>
        <tr>
            <td><strong>Total Products:</strong></td>
            <td>{{ number_format($data['inventory']['total_products']) }}</td>
        </tr>
        <tr>
            <td><strong>Low Stock Items:</strong></td>
            <td>{{ number_format($data['inventory']['low_stock_products']) }}</td>
        </tr>
        <tr>
            <td><strong>Out of Stock:</strong></td>
            <td>{{ number_format($data['inventory']['out_of_stock_products']) }}</td>
        </tr>
        <tr>
            <td><strong>Total Inventory Value:</strong></td>
            <td>UGX {{ number_format($data['inventory']['total_inventory_value'], 0) }}</td>
        </tr>
    </table>
</div>

<!-- ML Analysis Section -->
@if($data['executive_summary']['ml_insights_available'])
<div class="section">
    <h2>Machine Learning Insights</h2>
    <table>
        <tr>
            <td><strong>Customer Segments:</strong></td>
            <td>{{ number_format($data['ml_analysis']['customer_segments']['total_segments']) }}</td>
        </tr>
        <tr>
            <td><strong>Demand Predictions:</strong></td>
            <td>{{ number_format($data['ml_analysis']['demand_predictions']['total_predictions']) }}</td>
        </tr>
        <tr>
            <td><strong>Total Predicted Demand:</strong></td>
            <td>{{ number_format($data['ml_analysis']['demand_predictions']['total_predicted_demand']) }} units</td>
        </tr>
        <tr>
            <td><strong>Customer Clusters:</strong></td>
            <td>{{ count($data['ml_analysis']['customer_segments']['cluster_breakdown']) }}</td>
        </tr>
    </table>
</div>
@endif

<!-- Key Performance Indicators -->
<div class="section">
    <h2>Key Performance Indicators</h2>
    <ul>
        <li><strong>Revenue Growth:</strong> UGX {{ number_format($data['sales']['total_revenue'], 0) }} in revenue for the period</li>
        <li><strong>Order Fulfillment:</strong> {{ number_format($data['sales']['total_orders'] - $data['sales']['pending_orders']) }} of {{ number_format($data['sales']['total_orders']) }} orders completed</li>
        <li><strong>Inventory Health:</strong> {{ number_format(100 - (($data['inventory']['low_stock_products'] + $data['inventory']['out_of_stock_products']) / $data['inventory']['total_products']) * 100, 1) }}% products adequately stocked</li>
        @if($data['executive_summary']['ml_insights_available'])
        <li><strong>Data-Driven Insights:</strong> {{ number_format($data['ml_analysis']['customer_segments']['total_segments']) }} customers segmented for targeted marketing</li>
        @endif
    </ul>
</div>

<!-- Recommendations -->
<div class="section">
    <h2>Strategic Recommendations</h2>
    <ul>
        @if($data['inventory']['out_of_stock_products'] > 0)
        <li><strong>Urgent:</strong> Restock {{ $data['inventory']['out_of_stock_products'] }} out-of-stock products immediately</li>
        @endif
        
        @if($data['inventory']['low_stock_products'] > 0)
        <li><strong>Planning:</strong> Schedule reorders for {{ $data['inventory']['low_stock_products'] }} low-stock items</li>
        @endif
        
        @if($data['sales']['pending_orders'] > 0)
        <li><strong>Operations:</strong> Process {{ $data['sales']['pending_orders'] }} pending orders to improve fulfillment rate</li>
        @endif
        
        @if($data['executive_summary']['ml_insights_available'])
        <li><strong>Marketing:</strong> Leverage customer segmentation data for targeted campaigns</li>
        <li><strong>Inventory:</strong> Use demand predictions to optimize stock levels</li>
        @else
        <li><strong>Analytics:</strong> Run ML analysis to unlock customer insights and demand forecasting</li>
        @endif
    </ul>
</div>
