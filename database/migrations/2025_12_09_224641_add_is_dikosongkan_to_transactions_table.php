<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_dikosongkan')->default(false)->after('pengosongan_berakhir');
            $table->timestamp('dikosongkan_at')->nullable()->after('is_dikosongkan');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['is_dikosongkan', 'dikosongkan_at']);
        });
    }
};