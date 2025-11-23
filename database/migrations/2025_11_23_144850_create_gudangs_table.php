<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_gudang')->unique();
            $table->string('nama_gudang');
            $table->text('alamat');
            $table->string('kota');
            $table->string('provinsi');
            $table->string('kode_pos')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gudangs');
    }
};