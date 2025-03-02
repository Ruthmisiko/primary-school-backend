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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('contact_number');
            $table->string('designation');
            $table->string('email')->unique();
            $table->text('subjects_taught')->nullable();
            $table->string('assigned_class');
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
        Schema::drop('teachers');
    }
};
