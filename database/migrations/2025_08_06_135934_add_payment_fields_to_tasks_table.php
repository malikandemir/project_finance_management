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
            $table->boolean('is_paid')->default(false)->after('cost_percentage');
            $table->boolean('is_get_paid')->default(false)->after('is_paid');
            $table->unsignedBigInteger('payment_account_id')->nullable()->after('is_get_paid');
            $table->unsignedBigInteger('get_paid_account_id')->nullable()->after('payment_account_id');
            
            // Add foreign key constraints
            $table->foreign('payment_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('get_paid_account_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['payment_account_id']);
            $table->dropForeign(['get_paid_account_id']);
            $table->dropColumn(['is_paid', 'is_get_paid', 'payment_account_id', 'get_paid_account_id']);
        });
    }
};
