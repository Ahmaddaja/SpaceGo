<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_revenues', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->tinyInteger('month');
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->integer('total_raks_rented')->default(0);
            $table->timestamps();
            
            $table->unique(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_revenues');
    }
};