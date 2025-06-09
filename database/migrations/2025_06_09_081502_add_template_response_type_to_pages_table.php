<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, we need to drop and recreate the check constraint
        if (DB::getDriverName() === 'pgsql') {
            // Drop the existing check constraint
            DB::statement('ALTER TABLE pages DROP CONSTRAINT pages_response_type_check');

            // Add the new check constraint with template included
            DB::statement("ALTER TABLE pages ADD CONSTRAINT pages_response_type_check CHECK (response_type IN ('html', 'markdown', 'json', 'template'))");
        } else {
            // For other databases, modify the column
            Schema::table('pages', function (Blueprint $table) {
                $table->enum('response_type', ['html', 'markdown', 'json', 'template'])->default('html')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Drop the constraint and recreate without template
            DB::statement('ALTER TABLE pages DROP CONSTRAINT pages_response_type_check');
            DB::statement("ALTER TABLE pages ADD CONSTRAINT pages_response_type_check CHECK (response_type IN ('html', 'markdown', 'json'))");
        } else {
            Schema::table('pages', function (Blueprint $table) {
                $table->enum('response_type', ['html', 'markdown', 'json'])->default('html')->change();
            });
        }
    }
};
