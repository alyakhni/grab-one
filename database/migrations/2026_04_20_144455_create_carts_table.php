<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            /*
             * Human-friendly fleet identifier, for example GO-001.
             */
            $table->string('code')->unique();

            /*
             * Stable internal code such as 4_seater or 6_seater.
             */
            $table->string('cart_type');

            /*
             * Operational state only.
             * Reservation state is calculated from booking assignments
             * and booking date/time ranges.
             */
            $table->string('operational_status')->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['cart_type', 'operational_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};