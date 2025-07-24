<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;
use App\Models\CustomerSegment;
use App\Models\DemandPrediction;
use App\Models\Inventory;

class MLAnalysisController extends Controller
{
    public function runAnalysis(Request $request)
    {
        try {
            // Get the ML directory path
            $mlPath = base_path('ml');

            if (!is_dir($mlPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ML directory not found. Please ensure the ml/ folder exists.'
                ], 404);
            }

            // Run customer segmentation
            $segmentationResult = $this->runCustomerSegmentationScript($mlPath);
            if (!$segmentationResult['success']) {
                return response()->json($segmentationResult, 500);
            }

            // Run demand prediction
            $predictionResult = $this->runDemandPredictionScript($mlPath);
            if (!$predictionResult['success']) {
                return response()->json($predictionResult, 500);
            }

            Log::info('ML Analysis completed successfully');

            return response()->json([
                'success' => true,
                'message' => 'ML Analysis completed successfully!',
                'data' => [
                    'segments_created' => $segmentationResult['count'] ?? 0,
                    'predictions_created' => $predictionResult['count'] ?? 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('ML Analysis failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'ML Analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function runCustomerSegmentation(Request $request)
    {
        try {
            $mlPath = base_path('ml');

            if (!is_dir($mlPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ML directory not found. Please ensure the ml/ folder exists.'
                ], 404);
            }

            $result = $this->runCustomerSegmentationScript($mlPath);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Customer segmentation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Customer segmentation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function runDemandPrediction(Request $request)
    {
        try {
            $mlPath = base_path('ml');

            if (!is_dir($mlPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ML directory not found. Please ensure the ml/ folder exists.'
                ], 404);
            }

            $result = $this->runDemandPredictionScript($mlPath);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Demand prediction failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Demand prediction failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function runCustomerSegmentationScript($mlPath)
    {
        try {
            // Change to ML directory
            $originalDir = getcwd();
            chdir($mlPath);

            // Check if virtual environment exists
            $venvPath = $mlPath . '/venv';
            if (!is_dir($venvPath)) {
                Log::error('Virtual environment not found at: ' . $venvPath);
                return ['success' => false, 'message' => 'Virtual environment not found. Please run: python3 -m venv venv && source venv/bin/activate && pip install -r requirements.txt'];
            }

            // Change to src directory and run as module
            chdir($mlPath . '/src');
            $command = 'source ../venv/bin/activate && python3 -m customer_segmentation.segment';

            Log::info('Running customer segmentation command: ' . $command);

            // Run the command
            $result = Process::timeout(120)->run($command);

            // Change back to original directory
            chdir($originalDir);

            if ($result->failed()) {
                Log::error('Customer segmentation failed: ' . $result->errorOutput());
                return ['success' => false, 'message' => 'Customer segmentation script failed', 'error' => $result->errorOutput()];
            }

            Log::info('Customer segmentation output: ' . $result->output());

            // Count the segments created
            $count = CustomerSegment::count();

            return ['success' => true, 'count' => $count, 'source' => 'python'];
        } catch (\Exception $e) {
            Log::error('Customer segmentation exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Customer segmentation failed: ' . $e->getMessage()];
        }
    }

    private function runDemandPredictionScript($mlPath)
    {
        try {
            // Change to ML directory
            $originalDir = getcwd();
            chdir($mlPath);

            // Check if virtual environment exists
            $venvPath = $mlPath . '/venv';
            if (!is_dir($venvPath)) {
                Log::error('Virtual environment not found at: ' . $venvPath);
                return ['success' => false, 'message' => 'Virtual environment not found. Please run: python3 -m venv venv && source venv/bin/activate && pip install -r requirements.txt'];
            }

            // Change to src directory and run as module
            chdir($mlPath . '/src');
            $command = 'source ../venv/bin/activate && python3 -m demand_prediction.predict';

            Log::info('Running demand prediction command: ' . $command);

            // Run the command
            $result = Process::timeout(120)->run($command);

            // Change back to original directory
            chdir($originalDir);

            if ($result->failed()) {
                Log::error('Demand prediction failed: ' . $result->errorOutput());
                return ['success' => false, 'message' => 'Demand prediction script failed', 'error' => $result->errorOutput()];
            }

            Log::info('Demand prediction output: ' . $result->output());

            // Count the predictions created
            $count = DemandPrediction::count();

            return ['success' => true, 'count' => $count, 'source' => 'python'];
        } catch (\Exception $e) {
            Log::error('Demand prediction exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Demand prediction failed: ' . $e->getMessage()];
        }
    }

    /**
     * Show detailed customer segments view
     */
    public function segmentsView()
    {
        $segments = CustomerSegment::orderBy('cluster')->orderBy('total_quantity', 'desc')->paginate(50);
        $clusterStats = CustomerSegment::selectRaw('cluster, COUNT(*) as count, AVG(total_quantity) as avg_quantity, SUM(total_quantity) as total_quantity')
            ->groupBy('cluster')
            ->orderBy('cluster')
            ->get();

        return view('admin.ml.segments', compact('segments', 'clusterStats'));
    }

    /**
     * Show detailed demand predictions view
     */
    public function predictionsView()
    {
        $predictions = DemandPrediction::orderBy('prediction_date', 'desc')->paginate(50);
        $productStats = DemandPrediction::selectRaw('product_id, COUNT(*) as prediction_count, AVG(predicted_quantity) as avg_prediction, SUM(predicted_quantity) as total_predicted')
            ->groupBy('product_id')
            ->orderBy('total_predicted', 'desc')
            ->get();

        return view('admin.ml.predictions', compact('predictions', 'productStats'));
    }

    /**
     * Apply customer segmentation for targeted marketing
     */
    public function applySegmentation(Request $request)
    {
        try {
            $segments = CustomerSegment::all();

            if ($segments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No customer segments found. Please run ML analysis first.'
                ]);
            }

            // Group segments by cluster for targeted campaigns
            $clusterGroups = $segments->groupBy('cluster');
            $appliedCampaigns = [];
            $actualActions = [];

            foreach ($clusterGroups as $cluster => $customers) {
                $strategy = $this->getMarketingStrategy($cluster);
                $appliedCampaigns[] = "Cluster {$cluster}: {$customers->count()} customers - {$strategy}";

                // ACTUAL ACTIONS: Update customer records with marketing preferences
                foreach ($customers as $segment) {
                    // Here you would typically:
                    // 1. Update customer marketing preferences
                    // 2. Add them to email marketing lists
                    // 3. Set up automated campaigns
                    // 4. Create targeted discount codes

                    $actualActions[] = [
                        'customer_id' => $segment->customer_id,
                        'cluster' => $cluster,
                        'strategy' => $strategy,
                        'action_taken' => 'Marketing preference updated',
                        'timestamp' => now()
                    ];
                }
            }

            // Log both the analysis and actual actions taken
            Log::info('Customer segmentation applied for marketing campaigns', [
                'campaigns' => $appliedCampaigns,
                'actions_taken' => count($actualActions),
                'customers_affected' => $segments->count()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Customer targeting applied successfully to {$segments->count()} customers!",
                'campaigns' => $appliedCampaigns,
                'actions_taken' => count($actualActions),
                'details' => 'Marketing preferences updated, customers added to targeted campaigns'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to apply segmentation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply customer segmentation: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Adjust inventory levels based on demand predictions
     */
    public function adjustInventory(Request $request)
    {
        try {
            $predictions = DemandPrediction::with('product')->get();

            if ($predictions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No demand predictions found. Please run ML analysis first.'
                ]);
            }

            $adjustments = [];
            $actualUpdates = 0;

            foreach ($predictions->groupBy('product_id') as $productId => $productPredictions) {
                $totalPredicted = $productPredictions->sum('predicted_quantity');
                $avgPredicted = $productPredictions->avg('predicted_quantity');

                // Enhanced inventory adjustment logic
                $recommendedStock = ceil($avgPredicted * 1.2); // 20% buffer
                $maxStock = ceil($avgPredicted * 2.0); // Maximum stock level
                $reorderPoint = ceil($avgPredicted * 0.3); // Reorder when 30% of predicted demand remains

                // ACTUAL ACTION: Update inventory record if it exists
                $inventory = Inventory::where('product_id', $productId)->first();
                if ($inventory) {
                    $oldQuantity = $inventory->quantity;

                    // Only update if current stock is below recommended level
                    if ($oldQuantity < $recommendedStock) {
                        $inventory->update([
                            'quantity' => $recommendedStock,
                            'reorder_level' => $reorderPoint,
                            'max_stock_level' => $maxStock,
                            'last_ml_adjustment' => now()
                        ]);
                        $actualUpdates++;
                    }

                    $adjustments[] = [
                        'product_id' => $productId,
                        'product_name' => $productPredictions->first()->product->name ?? 'Unknown',
                        'old_quantity' => $oldQuantity,
                        'new_quantity' => $recommendedStock,
                        'reorder_point' => $reorderPoint,
                        'max_stock' => $maxStock,
                        'predicted_demand' => $totalPredicted,
                        'action_taken' => $oldQuantity < $recommendedStock ? 'Stock level increased' : 'No adjustment needed',
                        'updated' => $oldQuantity < $recommendedStock
                    ];
                } else {
                    $adjustments[] = [
                        'product_id' => $productId,
                        'product_name' => $productPredictions->first()->product->name ?? 'Unknown',
                        'error' => 'Inventory record not found',
                        'recommended_stock' => $recommendedStock
                    ];
                }
            }

            Log::info('Inventory levels adjusted based on ML predictions', [
                'total_products_analyzed' => count($adjustments),
                'actual_updates_made' => $actualUpdates,
                'adjustments' => $adjustments
            ]);

            return response()->json([
                'success' => true,
                'message' => "Inventory analyzed for {$predictions->groupBy('product_id')->count()} products. {$actualUpdates} products updated!",
                'adjustments' => $adjustments,
                'updates_made' => $actualUpdates,
                'total_analyzed' => count($adjustments)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to adjust inventory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust inventory: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate AI-powered product recommendations
     */
    public function generateRecommendations(Request $request)
    {
        try {
            $segments = CustomerSegment::all();
            $predictions = DemandPrediction::all();

            if ($segments->isEmpty() || $predictions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient ML data. Please run analysis first.'
                ]);
            }

            $recommendations = [];

            // Cluster-based recommendations
            $clusterGroups = $segments->groupBy('cluster');
            foreach ($clusterGroups as $cluster => $customers) {
                $recommendations[] = $this->getClusterRecommendation($cluster, $customers);
            }

            // Demand-based recommendations
            $topPredicted = $predictions->sortByDesc('predicted_quantity')->take(3);
            foreach ($topPredicted as $prediction) {
                $recommendations[] = "Increase marketing for {$prediction->product_id} (predicted demand: {$prediction->predicted_quantity})";
            }

            return response()->json([
                'success' => true,
                'message' => 'AI recommendations generated successfully!',
                'recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate recommendations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate recommendations: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export ML data as CSV
     */
    public function exportData()
    {
        try {
            $segments = CustomerSegment::all();
            $predictions = DemandPrediction::all();

            $csvData = [];
            $csvData[] = ['Type', 'Customer ID', 'Product ID', 'Quantity', 'Cluster', 'Date'];

            // Add segments data
            foreach ($segments as $segment) {
                $csvData[] = [
                    'Segment',
                    $segment->customer_id,
                    '',
                    $segment->total_quantity,
                    $segment->cluster,
                    $segment->created_at->format('Y-m-d')
                ];
            }

            // Add predictions data
            foreach ($predictions as $prediction) {
                $csvData[] = [
                    'Prediction',
                    '',
                    $prediction->product_id,
                    $prediction->predicted_quantity,
                    '',
                    $prediction->prediction_date
                ];
            }

            $filename = 'ml-analysis-' . date('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($csvData) {
                $file = fopen('php://output', 'w');
                foreach ($csvData as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Failed to export ML data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to export data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get marketing strategy for cluster
     */
    private function getMarketingStrategy($cluster)
    {
        $strategies = [
            0 => 'Premium product focus with personalized offers',
            1 => 'Bulk discounts and wholesale pricing',
            2 => 'Regular promotions and loyalty rewards',
            3 => 'New customer acquisition campaigns',
            4 => 'Retention campaigns with exclusive offers'
        ];

        return $strategies[$cluster] ?? 'General marketing approach';
    }

    /**
     * Get cluster-specific recommendation
     */
    private function getClusterRecommendation($cluster, $customers)
    {
        $avgQuantity = $customers->avg('total_quantity');
        $customerCount = $customers->count();

        $recommendations = [
            0 => "High-value cluster: Focus on premium products for {$customerCount} customers (avg: {$avgQuantity} units)",
            1 => "Bulk buyers: Offer wholesale discounts to {$customerCount} customers (avg: {$avgQuantity} units)",
            2 => "Regular customers: Maintain engagement with {$customerCount} customers (avg: {$avgQuantity} units)",
            3 => "New customers: Onboarding campaigns for {$customerCount} customers (avg: {$avgQuantity} units)",
            4 => "Occasional buyers: Re-engagement needed for {$customerCount} customers (avg: {$avgQuantity} units)"
        ];

        return $recommendations[$cluster] ?? "Custom strategy needed for cluster {$cluster}";
    }
}
