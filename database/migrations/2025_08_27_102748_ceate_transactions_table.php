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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Link to student
            $table->unsignedBigInteger('payment_id')->nullable(); // Optional link to payment/invoice
            $table->string('pesapal_merchant_reference')->unique(); // Your order id
            $table->string('pesapal_tracking_id')->nullable(); // Returned by Pesapal
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('KES');
            $table->string('status')->default('PENDING'); // PENDING, COMPLETED, FAILED
            $table->string('payment_method')->nullable(); // Mpesa, Card, Bank
            $table->json('raw_response')->nullable(); // Store Pesapal raw callback
            $table->timestamps();

            // Foreign Keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
