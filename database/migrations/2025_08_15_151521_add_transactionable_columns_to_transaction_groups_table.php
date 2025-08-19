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
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('transactionable_id')->nullable();
            $table->string('transactionable_type')->nullable();
            $table->index(['transactionable_id', 'transactionable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->dropIndex(['transactionable_id', 'transactionable_type']);
            $table->dropColumn(['transactionable_id', 'transactionable_type']);
        });
    }
};
