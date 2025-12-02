<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_renewal')->default(false)->after('transaction_status');
            $table->unsignedBigInteger('parent_transaction_id')->nullable()->after('is_renewal');
            $table->decimal('penalty_amount', 15, 2)->default(0)->after('amount');
            
            $table->foreign('parent_transaction_id')
                  ->references('id')
                  ->on('transactions')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['parent_transaction_id']);
            $table->dropColumn(['is_renewal', 'parent_transaction_id', 'penalty_amount']);
        });
    }
};