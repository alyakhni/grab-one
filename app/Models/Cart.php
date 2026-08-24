<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'cart_type',
        'operational_status',
        'notes',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(BookingCartAssignment::class);
    }

    public function bookingItems(): BelongsToMany
    {
        return $this->belongsToMany(
            BookingItem::class,
            'booking_cart_assignments'
        )->withTimestamps();
    }
}