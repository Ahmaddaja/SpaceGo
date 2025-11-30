<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rak')->unique();
            $table->string('nama_rak');
            $table->enum('jenis_rak', ['Heavy Duty', 'Medium Duty', 'Light Duty', 'Cantilever']);
            $table->text('deskripsi')->nullable();
            $table->integer('kapasitas_berat'); // kg
            $table->decimal('panjang', 8, 2); // meter
            $table->decimal('lebar', 8, 2); // meter
            $table->decimal('tinggi', 8, 2); // meter
            $table->integer('jumlah_tingkat');
            $table->string('lokasi_gudang');
            // $table->string('zona_gudang')->nullable();
            $table->decimal('harga_sewa_perbulan', 12, 2);
            $table->enum('status', ['tersedia', 'terisi', 'maintenance'])->default('tersedia');
            $table->string('foto')->nullable();
            $table->text('spesifikasi_tambahan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('durasi_sewa_hari')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raks');
    }
};