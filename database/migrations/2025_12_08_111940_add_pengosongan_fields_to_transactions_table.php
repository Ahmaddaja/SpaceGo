<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_pengosongan')->default(false)->after('is_renewal');
            $table->timestamp('pengosongan_dimulai')->nullable()->after('is_pengosongan');
            $table->timestamp('pengosongan_berakhir')->nullable()->after('pengosongan_dimulai');
        });

        Schema::table('raks', function (Blueprint $table) {
            // Ubah enum status untuk menambahkan 'pengosongan'
            DB::statement("ALTER TABLE raks MODIFY COLUMN status ENUM('tersedia', 'terisi', 'maintenance', 'pengosongan') DEFAULT 'tersedia'");
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['is_pengosongan', 'pengosongan_dimulai', 'pengosongan_berakhir']);
        });

        Schema::table('raks', function (Blueprint $table) {
            DB::statement("ALTER TABLE raks MODIFY COLUMN status ENUM('tersedia', 'terisi', 'maintenance') DEFAULT 'tersedia'");
        });
    }
};