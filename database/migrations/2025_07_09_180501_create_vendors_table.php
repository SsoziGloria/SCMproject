<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            Schema::create('vendors', function (Blueprint $table) {
    $table->id('vendor_id');
    $table->string('name', 100);
    $table->string('email', 100);
    $table->string('company_name', 150);
    $table->string('contact_person', 100)->nullable();
    $table->string('phone', 20)->nullable();
    $table->string('address', 255)->nullable();
    $table->string('bank_name', 100)->nullable();
    $table->string('account_number', 50)->nullable();
    $table->string('certification', 100)->nullable();
    $table->string('country', 50);
    $table->decimal('financial_score', 10, 2);
    $table->boolean('regulatory_compliance');
    $table->enum('reputation', ['Excellent', 'Good', 'Average', 'Poor']);
    $table->enum('validation_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
    $table->dateTime('visit_scheduled_at')->nullable();
    $table->timestamps();
});

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


