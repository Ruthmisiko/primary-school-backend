<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the userType enum to include 'parent'
        DB::statement("ALTER TABLE users MODIFY COLUMN userType ENUM('super_admin', 'admin', 'client', 'parent') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the userType enum to exclude 'parent'
        DB::statement("ALTER TABLE users MODIFY COLUMN userType ENUM('super_admin', 'admin', 'client') NOT NULL");
    }
};
