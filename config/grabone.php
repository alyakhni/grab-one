<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business Timezone
    |--------------------------------------------------------------------------
    */

    'timezone' => env('APP_TIMEZONE', 'America/Belize'),

    /*
    |--------------------------------------------------------------------------
    | Booking Rules
    |--------------------------------------------------------------------------
    */

    'booking_buffer_minutes' => 60,

    'default_cart_quantity' => 1,

    /*
    |--------------------------------------------------------------------------
    | Pickup Locations
    |--------------------------------------------------------------------------
    |
    | Keys are stable database codes. Labels are display values.
    |
    */

    'pickup_locations' => [
        'hotel' => 'My Hotel',
        'airport' => 'San Pedro Airport',
        'water_taxi' => 'Water Taxi Terminal',
        'store' => 'In-Store',
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking Statuses
    |--------------------------------------------------------------------------
    */

    'booking_statuses' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fleet Operational Statuses
    |--------------------------------------------------------------------------
    |
    | Reservation is intentionally not an operational status. Reservation
    | state is calculated from booking assignments and date/time overlap.
    |
    */

    'cart_operational_statuses' => [
        'active' => 'Active',
        'maintenance' => 'Maintenance',
        'inactive' => 'Inactive',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Statuses
    |--------------------------------------------------------------------------
    */

    'contact_statuses' => [
        'pending' => 'Pending',
        'working_on_it' => 'Working on it',
        'resolved' => 'Resolved',
    ],

];