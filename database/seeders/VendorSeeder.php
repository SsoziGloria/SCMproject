<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vendors')->insert([
            [
                'name' => 'Kampala Cocoa Ltd',
                'email' => 'info@kampalacocoa.com',
                'company_name' => 'Kampala Cocoa Ltd',
                'contact_person' => 'John Kintu',
                'phone' => '+256700000001',
                'address' => 'Plot 12, Cocoa Road, Kampala',
                'bank_name' => 'Stanbic Bank',
                'account_number' => '1002003001',
                'certification' => 'ISO 22000',
                'certification_status' => 'Valid',
                'compliance_status' => 'Compliant',
                'monthly_revenue' => 500000,
                'revenue' => 600000,
                'country' => 'Uganda',
                'financial_score' => 85.50,
                'regulatory_compliance' => true,
                'reputation' => 'Excellent',
                'validation_status' => 'Approved',
                'visit_date' => '2025-06-01',
                'pdf_path' => null,
                'retailer_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ChocoFarmers Co-op',
                'email' => 'contact@chocofarmers.coop',
                'company_name' => 'ChocoFarmers Cooperative',
                'contact_person' => 'Sarah Nambasa',
                'phone' => '+256700000002',
                'address' => 'Farm Lane, Hoima',
                'bank_name' => 'Centenary Bank',
                'account_number' => '2003004002',
                'certification' => 'Fairtrade',
                'certification_status' => 'Valid',
                'compliance_status' => 'Compliant',
                'monthly_revenue' => 2000000,
                'revenue' => 24000000,
                'country' => 'Uganda',
                'financial_score' => 78.00,
                'regulatory_compliance' => true,
                'reputation' => 'Good',
                'validation_status' => 'Approved',
                'visit_date' => '2025-06-15',
                'pdf_path' => null,
                'supplier_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
