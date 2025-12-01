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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('rak_id')->constrained('raks')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('payment_type')->nullable();
            $table->string('transaction_status')->default('pending');
            $table->string('fraud_status')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->text('snap_token')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->date('sewa_mulai')->nullable();
            $table->date('sewa_berakhir')->nullable();
            $table->timestamps();

            // Index untuk query yang sering digunakan
            $table->index('order_id');
            $table->index('user_id');
            $table->index('transaction_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};