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
        Schema::table('parents', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->unsignedBigInteger('student_id')->index()->after('name');
            $table->string('id_number')->unique()->nullable()->after('student_id');
            $table->string('phone_number')->after('id_number');
            $table->string('address')->nullable()->after('phone_number');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('phone_number');
            $table->string('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            //
        });
    }
};
