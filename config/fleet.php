<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public Fleet
    |--------------------------------------------------------------------------
    |
    | Array keys are stable internal cart type codes stored in the database.
    | Names, descriptions, images, and other public-facing information remain
    | source-controlled here and are never editable from the admin dashboard.
    |
    */

    'carts' => [

        '4_seater' => [
            'name' => '4-Seater Deluxe Cart',
            'booking_label' => '4-Seater Cart',
            'slug' => '4-seater-deluxe',
            'anchor' => 'book-4-seater',
            'image' => 'images/fleet/4-seater-deluxe-golf-cart-san-pedro.png',
            'alt' => '4 Seater Deluxe Golf Cart Rental Belize',
            'description' => 'Perfect for couples and small families exploring San Pedro. Lifted, rugged tires, and comfortable seating.',
            'features' => [
                'Fits up to 4 passengers',
                'Gas Powered (Full tank included)',
                'Free Bridge Pass',
            ],
            'badge' => 'Popular',
            'price_label' => 'Contact for Price',
        ],

        '6_seater' => [
            'name' => '6-Seater Family Cart',
            'booking_label' => '6-Seater Cart',
            'slug' => '6-seater-family',
            'anchor' => 'book-6-seater',
            'image' => 'images/fleet/6-seater-family-golf-cart-ambergris-caye.png',
            'alt' => '6 Seater Family Golf Cart Rental',
            'description' => 'Ideal for larger groups or families. Extended roof, extra space, and smooth ride for the whole crew.',
            'features' => [
                'Fits up to 6 passengers',
                'Extended Roof & Storage',
                'Free Bridge Pass',
            ],
            'badge' => null,
            'price_label' => 'Contact for Price',
        ],

    ],

];