<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #d98323, #b76e1f);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px 20px;
            border-radius: 0 0 8px 8px;
        }
        .report-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #d98323;
        }
        .button {
            display: inline-block;
            background: #d98323;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 ChocolateSCM Report</h1>
        <p>{{ $report->name }}</p>
    </div>
    
    <div class="content">
        <div class="report-info">
            <h2>Report Details</h2>
            <p><strong>Report Type:</strong> {{ ucfirst(str_replace('-', ' ', $report->type)) }}</p>
            <p><strong>Period:</strong> {{ date('M j, Y', strtotime($report->date_from)) }} - {{ date('M j, Y', strtotime($report->date_to)) }}</p>
            <p><strong>Format:</strong> {{ strtoupper($report->format) }}</p>
            <p><strong>Generated:</strong> {{ $report->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        
        <p>Hello,</p>
        
        <p>Your requested report <strong>"{{ $report->name }}"</strong> has been successfully generated and is attached to this email.</p>
        
        <p>This report contains business intelligence data for the period from {{ date('M j, Y', strtotime($report->date_from)) }} to {{ date('M j, Y', strtotime($report->date_to)) }}.</p>
        
        <h3>What's included:</h3>
        <ul>
            @if($report->type === 'sales')
                <li>Sales performance metrics</li>
                <li>Daily revenue breakdown</li>
                <li>Order fulfillment statistics</li>
            @elseif($report->type === 'inventory')
                <li>Current stock levels</li>
                <li>Low stock alerts</li>
                <li>Inventory valuation</li>
            @elseif($report->type === 'ml-analysis')
                <li>Customer segmentation insights</li>
                <li>Demand prediction analysis</li>
                <li>ML-powered recommendations</li>
            @elseif($report->type === 'comprehensive')
                <li>Complete business overview</li>
                <li>Sales, inventory, and ML insights</li>
                <li>Strategic recommendations</li>
            @else
                <li>Detailed {{ ucfirst(str_replace('-', ' ', $report->type)) }} analysis</li>
            @endif
        </ul>
        
        <p>If you have any questions about this report or need additional analysis, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <strong>ChocolateSCM Business Intelligence Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated report from ChocolateSCM Business Intelligence System</p>
        <p>Report ID: {{ $report->id }} | Generated on {{ now()->format('F j, Y') }}</p>
    </div>
</body>
</html>
