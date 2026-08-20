<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

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
}