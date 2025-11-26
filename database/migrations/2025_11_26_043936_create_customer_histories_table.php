<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('activity_type'); // PAYMENT_SUCCESS, NEW_RENTAL, RENTAL_EXTENSION, etc.
            $table->text('description');
            $table->json('additional_data')->nullable();
            $table->string('created_by')->default('system');
            $table->timestamps();

            // Indexes untuk performa
            $table->index('customer_id');
            $table->index('activity_type');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_histories');
    }
};