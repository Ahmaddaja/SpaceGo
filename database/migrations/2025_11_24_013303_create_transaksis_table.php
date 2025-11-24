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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()
                ->cascadeOnDelete()
                ->noActionOnUpdate();
            $table->string('kode_transaksi');
            $table->dateTime('tanggal');
            $table->unsignedInteger('total');
            $table->unsignedInteger('tunai');
            $table->unsignedBigInteger('kembalian');
            $table->enum('status', ['selesai', 'batal']);
            $table->unsignedInteger('subtotal');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
