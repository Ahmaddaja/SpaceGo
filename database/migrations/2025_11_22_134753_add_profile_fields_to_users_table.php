<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telepon', 20)->nullable()->after('email');
            $table->string('perusahaan')->nullable()->after('telepon');
            $table->text('alamat')->nullable()->after('perusahaan');
            $table->string('foto')->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telepon', 'perusahaan', 'alamat', 'foto']);
        });
    }
};