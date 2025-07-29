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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->text('description')->nullable()->after('title');
            $table->string('status')->default('pending')->after('description');
            $table->string('priority')->default('medium')->after('status');
            $table->date('due_date')->nullable()->after('priority');
            $table->boolean('is_completed')->default(false)->after('due_date');
            $table->foreignId('project_id')->constrained()->after('is_completed');
            $table->foreignId('created_by')->constrained('users')->after('project_id');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->after('created_by');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['assigned_to']);
            $table->dropColumn([
                'title',
                'description',
                'status',
                'priority',
                'due_date',
                'is_completed',
                'project_id',
                'created_by',
                'assigned_to',
                'deleted_at'
            ]);
        });
    }
};
