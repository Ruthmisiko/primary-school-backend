<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->double('amount', 15, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('payment_method')->nullable();
            $table->date('expense_date')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('expenses');
    }
};
