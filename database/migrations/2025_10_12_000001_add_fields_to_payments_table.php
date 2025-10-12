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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('payments', 'description')) {
                $table->text('description')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('payments', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable()->after('student_id');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            }
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('school_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('payments', 'callback_data')) {
                $table->json('callback_data')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'transaction_id', 'description', 'school_id', 'user_id', 'callback_data']);
        });
    }
};
