<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'cart_type',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(BookingCartAssignment::class);
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(
            Cart::class,
            'booking_cart_assignments'
        )->withTimestamps();
    }
}