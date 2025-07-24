<div class="section">
    <h2>Demand Forecast Overview</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-value">{{ number_format($data['total_predictions']) }}</span>
            <span class="stat-label">Total Predictions</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $data['forecast_accuracy'] }}</span>
            <span class="stat-label">Forecast Accuracy</span>
        </div>
    </div>
</div>

<div class="section">
    <h2>Product Demand Forecasts</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Total Predicted Demand</th>
                <th>Average Demand</th>
                <th>Recommended Stock</th>
                <th>Predictions Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['product_forecasts'] as $forecast)
                <tr>
                    <td>{{ $forecast['product_name'] }}</td>
                    <td>{{ number_format($forecast['total_predicted_demand']) }}</td>
                    <td>{{ number_format($forecast['average_predicted_demand'], 2) }}</td>
                    <td>{{ number_format($forecast['recommended_stock_level']) }}</td>
                    <td>{{ $forecast['prediction_count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h2>Inventory Recommendations</h2>
    <p>Based on the demand forecasts, the following inventory adjustments are recommended:</p>
    <ul>
        @foreach($data['product_forecasts'] as $forecast)
            <li><strong>{{ $forecast['product_name'] }}:</strong> 
                Maintain stock level of {{ number_format($forecast['recommended_stock_level']) }} units 
                ({{ number_format($forecast['average_predicted_demand'], 1) }} avg demand + 20% buffer)</li>
        @endforeach
    </ul>
</div>

<div class="section">
    <h2>Forecast Insights</h2>
    <ul>
        <li><strong>{{ count($data['product_forecasts']) }} products</strong> have active demand forecasts</li>
        <li><strong>{{ number_format($data['total_predictions']) }} total predictions</strong> generated from ML analysis</li>
        <li><strong>Recommended stock levels</strong> include 20% safety buffer above predicted demand</li>
        <li><strong>Regular forecast updates</strong> recommended for optimal inventory management</li>
    </ul>
</div>
