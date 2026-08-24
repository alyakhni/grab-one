<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_cart_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_item_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
             * Do not delete an actual fleet cart while booking history
             * still references it.
             */
            $table->foreignId('cart_id')
                ->constrained()
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(['booking_item_id', 'cart_id']);
            $table->index('cart_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_cart_assignments');
    }
};