<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id('vendor_id');
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('company_name', 150);
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('certification', 100)->nullable();
            $table->string('certification_status', 100)->nullable();
            $table->string('compliance_status', 100)->nullable();
            $table->float('monthly_revenue', 10)->nullable();
            $table->float('revenue', 10)->nullable();
            $table->string('country', 50);
            $table->decimal('financial_score', 10, 2)->default(0.00);
            $table->boolean('regulatory_compliance')->default(false);
            $table->enum('reputation', ['Excellent', 'Good', 'Average', 'Poor'])->default('Average');
            $table->enum('validation_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->date('visit_date')->nullable();
            $table->string('pdf_path')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};


