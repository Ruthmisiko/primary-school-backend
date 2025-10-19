<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new enum value 'parent' to existing type if it doesn't exist
        DB::statement("ALTER TYPE usertype ADD VALUE IF NOT EXISTS 'parent';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ⚠️ PostgreSQL doesn't allow removing enum values easily.
        // You’d need to recreate the type without 'parent' if you ever roll back.
        // Here’s the safe approach:
        DB::transaction(function () {
            // 1. Rename old type
            DB::statement("ALTER TYPE usertype RENAME TO usertype_old;");

            // 2. Create new type without 'parent'
            DB::statement("CREATE TYPE usertype AS ENUM('super_admin', 'admin', 'client');");

            // 3. Alter column to use new type
            DB::statement("ALTER TABLE users ALTER COLUMN \"userType\" TYPE usertype USING \"userType\"::text::usertype;");

            // 4. Drop old type
            DB::statement("DROP TYPE usertype_old;");
        });
    }
};
