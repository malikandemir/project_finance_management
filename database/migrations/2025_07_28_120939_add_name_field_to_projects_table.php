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
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('projects', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('projects', 'start_date')) {
                $table->date('start_date')->nullable()->after('description');
            }
            if (!Schema::hasColumn('projects', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('projects', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('end_date');
            }
            if (!Schema::hasColumn('projects', 'responsible_user_id')) {
                $table->foreignId('responsible_user_id')->nullable()->after('is_active')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['responsible_user_id']);
            $table->dropColumn(['name', 'description', 'start_date', 'end_date', 'is_active', 'responsible_user_id']);
        });
    }
};
