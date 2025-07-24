<div class="section">
    <h2>Machine Learning Analysis Overview</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['customer_segments']['total_segments']) }}</span>
            <span class="stat-label">Customer Segments</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['demand_predictions']['total_predictions']) }}</span>
            <span class="stat-label">Demand Predictions</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['demand_predictions']['total_predicted_demand']) }}</span>
            <span class="stat-label">Total Predicted Demand</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['demand_predictions']['avg_predicted_demand'], 1) }}</span>
            <span class="stat-label">Avg Predicted Demand</span>
        </div>
    </div>
</div>

<div class="section">
    <h2>Customer Segmentation Analysis</h2>
    <table>
        <thead>
            <tr>
                <th>Cluster</th>
                <th>Customer Count</th>
                <th>Avg Quantity</th>
                <th>Total Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['customer_segments']['cluster_breakdown'] as $cluster => $clusterData)
                <tr>
                    <td>Cluster {{ $cluster }}</td>
                    <td>{{ number_format($clusterData['count']) }}</td>
                    <td>{{ number_format($clusterData['avg_quantity'], 2) }}</td>
                    <td>{{ number_format($clusterData['total_quantity']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h2>Top Predicted Products</h2>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Predicted Demand</th>
                <th>Percentage of Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['demand_predictions']['top_predicted_products'] as $productId => $demand)
                <tr>
                    <td>Product {{ $productId }}</td>
                    <td>{{ number_format($demand) }}</td>
                    <td>{{ number_format(($demand / $data['demand_predictions']['total_predicted_demand']) * 100, 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h2>ML Insights Summary</h2>
    <ul>
        <li><strong>{{ count($data['customer_segments']['cluster_breakdown']) }} customer clusters</strong> identified for targeted marketing</li>
        <li><strong>{{ number_format($data['demand_predictions']['total_predictions']) }} demand predictions</strong> generated for inventory planning</li>
        <li><strong>Average predicted demand:</strong> {{ number_format($data['demand_predictions']['avg_predicted_demand'], 1) }} units per prediction</li>
        <li><strong>Data-driven insights</strong> available for business optimization</li>
    </ul>
</div>
