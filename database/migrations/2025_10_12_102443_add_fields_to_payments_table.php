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
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('student_id');
            $table->string('payment_method')->nullable()->after('currency');
            $table->string('transaction_id')->nullable()->after('payment_method');
            $table->string('description')->nullable()->after('transaction_id');
            $table->json('callback_data')->nullable()->after('description');
            $table->unsignedBigInteger('school_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'payment_method', 'transaction_id', 'description', 'callback_data','school_id']);
        });
    }
};
