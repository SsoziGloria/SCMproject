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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->date('date_from');
            $table->date('date_to');
            $table->string('format');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('generated_by');
            $table->longText('data')->nullable();
            $table->string('file_path')->nullable();
            $table->text('email_recipients')->nullable();
            $table->string('schedule_frequency')->nullable();
            $table->timestamps();

            $table->foreign('generated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
