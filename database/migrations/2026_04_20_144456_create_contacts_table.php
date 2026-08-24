<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            /*
             * A contact message may match an existing customer.
             * A contact message by itself does not automatically create one.
             */
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('phone', 50);
            $table->text('message');

            $table->string('status')->default('pending');
            $table->boolean('is_read')->default(false);

            $table->timestamps();

            $table->index(['status', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};