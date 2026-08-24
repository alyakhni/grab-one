<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
             * Stable internal code such as:
             * 4_seater
             * 6_seater
             *
             * Display labels remain controlled by code/config.
             */
            $table->string('cart_type');

            $table->unsignedSmallInteger('quantity')->default(1);

            $table->timestamps();

            /*
             * One row per cart type within a booking.
             * A mixed booking is represented by two rows.
             */
            $table->unique(['booking_id', 'cart_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};