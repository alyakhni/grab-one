<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
             * Transactional customer snapshot.
             * These values remain attached to the booking even if the
             * customer's current contact information changes later.
             */
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 50);
            $table->string('hotel_name')->nullable();

            /*
             * pickup_at and return_at are business-local datetimes.
             * Grab One operates in America/Belize.
             */
            $table->string('pickup_location');
            $table->dateTime('pickup_at');
            $table->dateTime('return_at');

            $table->string('flight_number')->nullable();
            $table->text('special_notes')->nullable();

            $table->decimal('total_price', 10, 2)->default(0.00);
            $table->string('status')->default('pending');

            $table->timestamps();

            $table->index(['status', 'pickup_at', 'return_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};