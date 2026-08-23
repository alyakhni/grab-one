<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business Timezone
    |--------------------------------------------------------------------------
    |
    | All booking, pickup, return, fleet availability, and operational
    | dashboard times are interpreted and displayed in Belize time.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'America/Belize'),

    /*
    |--------------------------------------------------------------------------
    | Booking Buffer
    |--------------------------------------------------------------------------
    |
    | Operational time reserved between two rentals of the same cart.
    | This is controlled in code, not from the admin dashboard.
    |
    */

    'booking_buffer_minutes' => 60,

];