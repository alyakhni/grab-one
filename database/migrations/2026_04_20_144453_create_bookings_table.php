<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // بيانات العميل (من الفورم)
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('hotel_name')->nullable();
            
            // تفاصيل الحجز (من الفورم)
            $table->string('pickup_location'); 
            $table->dateTime('pickup_date');
            $table->dateTime('return_date');
            $table->string('cart_type'); // 4-Seater أو 6-Seater
            $table->text('special_notes')->nullable(); // حقل Message الاختياري في الفورم
            
            // حقول برمجية وإدارية (لا يراها العميل)
            $table->string('flight_number')->nullable(); // تركته احتياطياً لو طلبته لاحقاً للسياح القادمين من المطار
            $table->integer('total_days')->default(1); // سنحسبه برمجياً لاحقاً
            $table->decimal('total_price', 10, 2)->default(0.00); // يضعها الآدمن أو تحسب برمجياً
            $table->string('status')->default('pending'); // pending, confirmed, cancelled
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
