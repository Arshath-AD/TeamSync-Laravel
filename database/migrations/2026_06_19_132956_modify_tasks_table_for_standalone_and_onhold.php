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
        DB::statement("ALTER TABLE tasks MODIFY COLUMN project_id bigint unsigned NULL");
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Pending', 'In Progress', 'On Hold', 'Completed') DEFAULT 'Pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY COLUMN project_id bigint unsigned NOT NULL");
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending'");
    }
};
