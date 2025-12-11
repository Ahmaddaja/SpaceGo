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
        Schema::table('tagihans', function (Blueprint $table) {
            // Status rak yang sedang disewa
            $table->enum('status_rak', [
                'tersedia',      // Belum disewa / sudah dikosongkan
                'terisi',        // Sedang disewa aktif
                'masa_tenggang', // Lewat jatuh tempo tapi masih dalam grace period (3 hari)
                'terlambat',     // Lewat grace period, kena denda
                'pengosongan',   // Sudah 30 hari terlambat, masuk masa pengosongan 7 hari
                'dikosongkan'    // Sudah lewat 37 hari total, rak dikosongkan
            ])->default('tersedia')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropColumn('status_rak');
        });
    }
};