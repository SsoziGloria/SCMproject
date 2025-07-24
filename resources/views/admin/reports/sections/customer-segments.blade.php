<div class="section">
    <h2>Customer Segmentation Overview</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['total_customers_segmented']) }}</span>
            <span class="stat-label">Customers Segmented</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ count($data['clusters']) }}</span>
            <span class="stat-label">Customer Clusters</span>
        </div>
    </div>
</div>

@foreach($data['clusters'] as $cluster)
<div class="section">
    <h2>Cluster {{ $cluster['cluster_number'] }} - {{ $cluster['customer_count'] }} Customers</h2>
    
    <table>
        <tr>
            <td><strong>Customer Count:</strong></td>
            <td>{{ number_format($cluster['customer_count']) }}</td>
        </tr>
        <tr>
            <td><strong>Average Quantity:</strong></td>
            <td>{{ number_format($cluster['avg_quantity'], 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total Quantity:</strong></td>
            <td>{{ number_format($cluster['total_quantity']) }}</td>
        </tr>
        <tr>
            <td><strong>Recommended Strategy:</strong></td>
            <td><em>{{ $cluster['recommended_strategy'] }}</em></td>
        </tr>
    </table>

    <h3>Sample Customers in This Cluster</h3>
    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Total Quantity</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cluster['customers'] as $customer)
                <tr>
                    <td>{{ $customer['customer_id'] }}</td>
                    <td>{{ number_format($customer['total_quantity']) }}</td>
                    <td>UGX {{ number_format($customer['total_amount'], 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach

<div class="section">
    <h2>Marketing Recommendations</h2>
    <ul>
        @foreach($data['clusters'] as $cluster)
            <li><strong>Cluster {{ $cluster['cluster_number'] }}:</strong> {{ $cluster['recommended_strategy'] }} ({{ $cluster['customer_count'] }} customers)</li>
        @endforeach
    </ul>
</div>
