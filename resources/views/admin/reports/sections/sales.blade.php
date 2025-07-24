<div class="section">
    <h2>Sales Overview</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-value">UGX {{ number_format($data['total_revenue'], 0) }}</span>
            <span class="stat-label">Total Revenue</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['total_orders']) }}</span>
            <span class="stat-label">Total Orders</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['pending_orders']) }}</span>
            <span class="stat-label">Pending Orders</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">UGX {{ number_format($data['average_order_value'], 0) }}</span>
            <span class="stat-label">Avg Order Value</span>
        </div>
    </div>
</div>

<div class="section">
    <h2>Daily Sales Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Orders</th>
                <th>Revenue</th>
                <th>Avg Order Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['daily_breakdown'] as $date => $dayData)
                <tr>
                    <td>{{ date('M j, Y', strtotime($date)) }}</td>
                    <td>{{ $dayData['orders'] }}</td>
                    <td>UGX {{ number_format($dayData['revenue'], 0) }}</td>
                    <td>UGX {{ $dayData['orders'] > 0 ? number_format($dayData['revenue'] / $dayData['orders'], 0) : '0.00' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h2>Sales Summary</h2>
    <table>
        <tr>
            <td><strong>Total Orders:</strong></td>
            <td>{{ number_format($data['total_orders']) }}</td>
        </tr>
        <tr>
            <td><strong>Pending Orders:</strong></td>
            <td>{{ number_format($data['pending_orders']) }}</td>
        </tr>
        <tr>
            <td><strong>Cancelled Orders:</strong></td>
            <td>{{ number_format($data['cancelled_orders']) }}</td>
        </tr>
        <tr>
            <td><strong>Total Revenue:</strong></td>
            <td>UGX {{ number_format($data['total_revenue'], 0) }}</td>
        </tr>
        <tr>
            <td><strong>Average Order Value:</strong></td>
            <td>UGX {{ number_format($data['average_order_value'], 0) }}</td>
        </tr>
    </table>
</div>
