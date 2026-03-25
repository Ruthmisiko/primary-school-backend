<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrmStaffsTable extends Migration
{
    public function up(): void
    {
        Schema::create('hrmstaffs', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->unsignedBigInteger('school_id');
            $table->string('staff_number')->unique();
            $table->string('shift_number')->nullable();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->date('date_hired')->nullable();
            $table->date('contract_end')->nullable();
            $table->string('kra_pin')->nullable();
            $table->string('nssf_number')->nullable();
            $table->string('nhif_number')->nullable();
            $table->decimal('basic_salary', 12, 2)->default(0.00);
            $table->decimal('allowances', 12, 2)->default(0.00);
            $table->decimal('deductions', 12, 2)->default(0.00);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrmstaffs');
    }
}

