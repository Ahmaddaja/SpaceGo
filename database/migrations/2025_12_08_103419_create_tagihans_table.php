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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('tagihan_code')->unique(); // BILL-xxxxx
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rak_id')->constrained('raks')->cascadeOnDelete();
            
            // Detail Tagihan
            $table->decimal('harga_sewa', 15, 2);
            $table->decimal('penalty_amount', 15, 2)->default(0);
            $table->decimal('total_tagihan', 15, 2);
            
            // Status & Type
            $table->enum('status', ['pending', 'settlement', 'expired', 'deny', 'cancel'])->default('pending');
            $table->enum('type', ['sewa_baru', 'renewal'])->default('sewa_baru');
            $table->boolean('is_renewal')->default(false);
            
            // Waktu
            $table->timestamp('created_at_db')->useCurrent(); // Waktu create dari DB
            $table->timestamp('expired_at')->nullable(); // 24 jam setelah created_at_db
            $table->timestamp('paid_at')->nullable(); // Waktu bayar
            $table->timestamp('cancelled_at')->nullable();
            
            // Info Sewa
            $table->date('sewa_mulai')->nullable();
            $table->date('sewa_berakhir')->nullable();
            
            // Parent untuk renewal
            $table->foreignId('parent_tagihan_id')->nullable()->constrained('tagihan')->nullOnDelete();
            
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index(['user_id', 'status']);
            $table->index(['status', 'expired_at']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};