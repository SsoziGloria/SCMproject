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
        Schema::create('vendor_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->references('vendor_id')->on('vendors')->onDelete('cascade');
            $table->string('original_filename');
            $table->string('file_path');
            $table->bigInteger('file_size');
            $table->boolean('is_valid')->default(false);
            $table->json('validation_results')->nullable();
            $table->text('validation_message')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'created_at']);
            $table->index(['is_valid', 'validated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_validations');
    }
};