<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomerClusterSummary;

class CustomerClusterSummarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
        [
            'cluster' => 0,
            'description' => 'Loyal high-value customers who buy frequently in large quantities',
            'customer_count' => 213,
            'product_types' => 'Packs of Dark Chocos,Drinking Coco, Organic Choco Syrup',
            'recommendation_strategy' => 'Offer bulk packages for premium items like Organic Choco Syrup'
        ],
        [
            'cluster' => 1,
            'description' => 'New or Occasional customers with small purchases',
            'customer_count' => 431,
            'product_types' => '50% Dark Bites, Starter Packs of Eclairs',
            'recommendation_strategy' => 'Offer discounts and suggest trial-sized items like Eclairs.'
        ],
        [
            'cluster' => 2,
            'description' => 'Bulk/wholesale buyers - few purchases but in bulk',
            'customer_count' => 82,
            'product_types' => 'Bulk Boxes of Drinking Coco, Packs of Milk Chips',
            'recommendation_strategy' => 'Promote wholesale discounts and restocking reminders'
        ],
        [
            'cluster' => 3,
            'description' => 'Moderate customers - average frequency and size of purchases',
            'customer_count' => 368,
            'product_types' => 'Gift Packs of White Choco, Milk Chips, 50% Dark Bites',
            'recommendation_strategy' => 'Suggest cross-sell combos and seasonal offers'
        ],
    ];

    foreach ($data as $entry) {
        CustomerClusterSummary::create($entry);
    }
}
}
