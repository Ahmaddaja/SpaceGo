<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_transaction_id')->nullable()->after('user_id');
            $table->boolean('is_renewal')->default(false)->after('parent_transaction_id');
            
            // Tambahkan foreign key constraint
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
            $table->dropColumn(['parent_transaction_id', 'is_renewal']);
        });
    }
};