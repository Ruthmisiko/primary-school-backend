<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClassIdColumnOnStudentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Correct MySQL syntax
            $table->bigInteger('class_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Rollback to original type (assuming it was integer)
            $table->integer('class_id')->change();
        });
    }
}
