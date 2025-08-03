<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE students ALTER COLUMN class_id TYPE BIGINT USING class_id::BIGINT');
    }

    public function down()
    {
        DB::statement('ALTER TABLE students ALTER COLUMN class_id TYPE VARCHAR');
    }
};
