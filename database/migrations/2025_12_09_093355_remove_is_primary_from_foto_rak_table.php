<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('foto_rak', function (Blueprint $table) {
            // Hapus index terlebih dahulu
            $table->dropIndex(['rak_id', 'is_primary']);
            
            // Hapus kolom is_primary
            $table->dropColumn('is_primary');
        });
    }

    public function down(): void
    {
        Schema::table('foto_rak', function (Blueprint $table) {
            // Tambahkan kembali kolom is_primary
            $table->boolean('is_primary')->default(false)->after('path');
            
            // Tambahkan kembali index
            $table->index(['rak_id', 'is_primary']);
        });
    }
};