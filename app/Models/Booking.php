<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public const PICKUP_LOCATIONS = [
        'My Hotel' => 'My Hotel',
        'San Pedro Airport' => 'San Pedro Airport',
        'Water Taxi Terminal' => 'Water Taxi Terminal',
        'In-Store' => 'In-Store',
    ];

    public const CART_TYPES = [
        '4-Seater' => '4-Seater Cart',
        '6-Seater' => '6-Seater Cart',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'hotel_name',
        'pickup_location',
        'pickup_date',
        'return_date',
        'cart_type',
        'special_notes',
        'flight_number',
        'total_days',
        'total_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'datetime',
            'return_date' => 'datetime',
            'total_days' => 'integer',
            'total_price' => 'decimal:2',
        ];
    }
}