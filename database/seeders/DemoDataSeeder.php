<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Cart;
use App\Models\Contact;
use App\Models\Customer;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $marker = '[GRABONE-DEMO]';

        $timezone = (string) config(
            'grabone.timezone',
            config(
                'app.timezone',
                'America/Belize'
            )
        );

        $now = CarbonImmutable::now($timezone)
            ->setSecond(0);

        $baseHour = $now
            ->setMinute(0)
            ->setSecond(0);

        DB::transaction(function () use (
            $marker,
            $now,
            $baseHour
        ): void {
            /*
             * Remove only data created by this demo seeder.
             * Real operational data and admin users are untouched.
             */
            Contact::query()
                ->where(
                    'message',
                    'like',
                    $marker.'%'
                )
                ->delete();

            Booking::query()
                ->where(
                    'special_notes',
                    'like',
                    $marker.'%'
                )
                ->delete();

            Cart::query()
                ->where(
                    'code',
                    'like',
                    'DEMO-%'
                )
                ->delete();

            Customer::query()
                ->where(
                    'notes',
                    'like',
                    $marker.'%'
                )
                ->delete();

            /*
             * Customers
             */
            $emma = Customer::create([
                'name' => 'Emma Carter',
                'email' => 'emma.demo@example.com',
                'email_normalized' => 'emma.demo@example.com',
                'phone' => '+5016101001',
                'phone_normalized' => '+5016101001',
                'notes' =>
                    $marker.' Returning demo customer.',
            ]);

            $daniel = Customer::create([
                'name' => 'Daniel Lopez',
                'email' => 'daniel.demo@example.com',
                'email_normalized' => 'daniel.demo@example.com',
                'phone' => '+5016101002',
                'phone_normalized' => '+5016101002',
                'notes' =>
                    $marker.' Demo customer with active rental.',
            ]);

            $sofia = Customer::create([
                'name' => 'Sofia Martinez',
                'email' => 'sofia.demo@example.com',
                'email_normalized' => 'sofia.demo@example.com',
                'phone' => '+5016101003',
                'phone_normalized' => '+5016101003',
                'notes' =>
                    $marker.' Demo customer with mixed request.',
            ]);

            $chris = Customer::create([
                'name' => 'Chris Walker',
                'email' => 'chris.demo@example.com',
                'email_normalized' => 'chris.demo@example.com',
                'phone' => '+5016101004',
                'phone_normalized' => '+5016101004',
                'notes' =>
                    $marker.' Demo confirmed mixed booking.',
            ]);

            $maya = Customer::create([
                'name' => 'Maya Chen',
                'email' => 'maya.demo@example.com',
                'email_normalized' => 'maya.demo@example.com',
                'phone' => '+5016101005',
                'phone_normalized' => '+5016101005',
                'notes' =>
                    $marker.' Demo future confirmed customer.',
            ]);

            /*
             * Actual fleet
             */
            $cartData = [
                [
                    'code' => 'DEMO-4-01',
                    'cart_type' => '4_seater',
                    'operational_status' => 'active',
                    'notes' =>
                        $marker.' Active 4-seater.',
                ],
                [
                    'code' => 'DEMO-4-02',
                    'cart_type' => '4_seater',
                    'operational_status' => 'active',
                    'notes' =>
                        $marker.' Active 4-seater.',
                ],
                [
                    'code' => 'DEMO-4-03',
                    'cart_type' => '4_seater',
                    'operational_status' => 'active',
                    'notes' =>
                        $marker.' Active 4-seater.',
                ],
                [
                    'code' => 'DEMO-4-04',
                    'cart_type' => '4_seater',
                    'operational_status' => 'maintenance',
                    'notes' =>
                        $marker.' Maintenance test cart.',
                ],
                [
                    'code' => 'DEMO-6-01',
                    'cart_type' => '6_seater',
                    'operational_status' => 'active',
                    'notes' =>
                        $marker.' Active 6-seater.',
                ],
                [
                    'code' => 'DEMO-6-02',
                    'cart_type' => '6_seater',
                    'operational_status' => 'active',
                    'notes' =>
                        $marker.' Active 6-seater.',
                ],
                [
                    'code' => 'DEMO-6-03',
                    'cart_type' => '6_seater',
                    'operational_status' => 'maintenance',
                    'notes' =>
                        $marker.' Maintenance test cart.',
                ],
                [
                    'code' => 'DEMO-6-04',
                    'cart_type' => '6_seater',
                    'operational_status' => 'inactive',
                    'notes' =>
                        $marker.' Inactive test cart.',
                ],
            ];

            $carts = collect();

            foreach ($cartData as $data) {
                $cart = Cart::create($data);

                $carts->put(
                    $cart->code,
                    $cart
                );
            }

            /*
             * Helper for creating booking + items + assignments.
             */
            $createBooking = function (
                array $data,
                array $items,
                array $assignments = []
            ) use (
                $carts
            ): Booking {
                $booking = Booking::create(
                    $data
                );

                foreach ($items as $itemData) {
                    $item = $booking
                        ->items()
                        ->create(
                            $itemData
                        );

                    foreach (
                        $assignments[
                            $itemData['cart_type']
                        ] ?? []
                        as $cartCode
                    ) {
                        $cart = $carts->get(
                            $cartCode
                        );

                        if (! $cart) {
                            throw new \RuntimeException(
                                "Demo cart {$cartCode} was not found."
                            );
                        }

                        $item
                            ->assignments()
                            ->create([
                                'cart_id' =>
                                    $cart->id,
                            ]);
                    }
                }

                return $booking;
            };

            /*
             * 1. CURRENT confirmed rental.
             */
            $createBooking(
                [
                    'customer_id' =>
                        $daniel->id,

                    'full_name' =>
                        $daniel->name,

                    'email' =>
                        $daniel->email,

                    'phone' =>
                        $daniel->phone,

                    'hotel_name' =>
                        'SunBreeze Hotel',

                    'pickup_location' =>
                        'hotel',

                    'pickup_at' =>
                        $baseHour
                            ->subHour(),

                    'return_at' =>
                        $baseHour
                            ->addHours(4),

                    'flight_number' =>
                        null,

                    'special_notes' =>
                        $marker.' CURRENT confirmed rental.',

                    'total_price' =>
                        180.00,

                    'status' =>
                        'confirmed',
                ],
                [
                    [
                        'cart_type' =>
                            '4_seater',

                        'quantity' =>
                            1,
                    ],
                ],
                [
                    '4_seater' => [
                        'DEMO-4-01',
                    ],
                ]
            );

            /*
             * 2. Pending request later today.
             */
            $createBooking(
                [
                    'customer_id' =>
                        $emma->id,

                    'full_name' =>
                        $emma->name,

                    'email' =>
                        $emma->email,

                    'phone' =>
                        $emma->phone,

                    'hotel_name' =>
                        'Victoria House',

                    'pickup_location' =>
                        'hotel',

                    'pickup_at' =>
                        $baseHour
                            ->addHours(6),

                    'return_at' =>
                        $baseHour
                            ->addHours(12),

                    'flight_number' =>
                        '2M 2054',

                    'special_notes' =>
                        $marker.' Pending 4-seater request.',

                    'total_price' =>
                        0.00,

                    'status' =>
                        'pending',
                ],
                [
                    [
                        'cart_type' =>
                            '4_seater',

                        'quantity' =>
                            1,
                    ],
                ]
            );

            /*
             * 3. Pending MIX request tomorrow.
             */
            $tomorrowPickup = $now
                ->addDay()
                ->setTime(9, 30);

            $tomorrowReturn = $now
                ->addDay()
                ->setTime(17, 30);

            $createBooking(
                [
                    'customer_id' =>
                        $sofia->id,

                    'full_name' =>
                        $sofia->name,

                    'email' =>
                        $sofia->email,

                    'phone' =>
                        $sofia->phone,

                    'hotel_name' =>
                        'Alaia Belize',

                    'pickup_location' =>
                        'airport',

                    'pickup_at' =>
                        $tomorrowPickup,

                    'return_at' =>
                        $tomorrowReturn,

                    'flight_number' =>
                        '9N 221',

                    'special_notes' =>
                        $marker.' Pending MIX request.',

                    'total_price' =>
                        0.00,

                    'status' =>
                        'pending',
                ],
                [
                    [
                        'cart_type' =>
                            '4_seater',

                        'quantity' =>
                            1,
                    ],
                    [
                        'cart_type' =>
                            '6_seater',

                        'quantity' =>
                            1,
                    ],
                ]
            );

            /*
             * 4. Future confirmed 6-seater.
             */
            $futureSixPickup = $now
                ->addDay()
                ->setTime(12, 0);

            $futureSixReturn = $now
                ->addDay()
                ->setTime(20, 0);

            $createBooking(
                [
                    'customer_id' =>
                        $maya->id,

                    'full_name' =>
                        $maya->name,

                    'email' =>
                        $maya->email,

                    'phone' =>
                        $maya->phone,

                    'hotel_name' =>
                        'Grand Caribe Belize',

                    'pickup_location' =>
                        'water_taxi',

                    'pickup_at' =>
                        $futureSixPickup,

                    'return_at' =>
                        $futureSixReturn,

                    'flight_number' =>
                        null,

                    'special_notes' =>
                        $marker.' Future confirmed 6-seater.',

                    'total_price' =>
                        225.00,

                    'status' =>
                        'confirmed',
                ],
                [
                    [
                        'cart_type' =>
                            '6_seater',

                        'quantity' =>
                            1,
                    ],
                ],
                [
                    '6_seater' => [
                        'DEMO-6-01',
                    ],
                ]
            );

            /*
             * 5. Future confirmed MIX booking.
             */
            $mixedPickup = $now
                ->addDays(2)
                ->setTime(10, 0);

            $mixedReturn = $now
                ->addDays(2)
                ->setTime(18, 0);

            $createBooking(
                [
                    'customer_id' =>
                        $chris->id,

                    'full_name' =>
                        $chris->name,

                    'email' =>
                        $chris->email,

                    'phone' =>
                        $chris->phone,

                    'hotel_name' =>
                        'Mahogany Bay Resort',

                    'pickup_location' =>
                        'hotel',

                    'pickup_at' =>
                        $mixedPickup,

                    'return_at' =>
                        $mixedReturn,

                    'flight_number' =>
                        null,

                    'special_notes' =>
                        $marker.' Confirmed MIX booking.',

                    'total_price' =>
                        375.00,

                    'status' =>
                        'confirmed',
                ],
                [
                    [
                        'cart_type' =>
                            '4_seater',

                        'quantity' =>
                            1,
                    ],
                    [
                        'cart_type' =>
                            '6_seater',

                        'quantity' =>
                            1,
                    ],
                ],
                [
                    '4_seater' => [
                        'DEMO-4-03',
                    ],
                    '6_seater' => [
                        'DEMO-6-02',
                    ],
                ]
            );

            /*
             * 6. Completed historical booking.
             * Assignment is intentionally kept for history.
             */
            $completedPickup = $now
                ->subDays(5)
                ->setTime(9, 0);

            $completedReturn = $now
                ->subDays(5)
                ->setTime(17, 0);

            $createBooking(
                [
                    'customer_id' =>
                        $emma->id,

                    'full_name' =>
                        $emma->name,

                    'email' =>
                        $emma->email,

                    'phone' =>
                        $emma->phone,

                    'hotel_name' =>
                        'SunBreeze Suites',

                    'pickup_location' =>
                        'hotel',

                    'pickup_at' =>
                        $completedPickup,

                    'return_at' =>
                        $completedReturn,

                    'flight_number' =>
                        null,

                    'special_notes' =>
                        $marker.' Completed historical booking.',

                    'total_price' =>
                        160.00,

                    'status' =>
                        'completed',
                ],
                [
                    [
                        'cart_type' =>
                            '4_seater',

                        'quantity' =>
                            1,
                    ],
                ],
                [
                    '4_seater' => [
                        'DEMO-4-02',
                    ],
                ]
            );

            /*
             * 7. Cancelled future booking.
             * Assignment remains as historical record but does not
             * block availability.
             */
            $cancelledPickup = $now
                ->addDays(3)
                ->setTime(11, 0);

            $cancelledReturn = $now
                ->addDays(3)
                ->setTime(15, 0);

            $createBooking(
                [
                    'customer_id' =>
                        $sofia->id,

                    'full_name' =>
                        $sofia->name,

                    'email' =>
                        $sofia->email,

                    'phone' =>
                        $sofia->phone,

                    'hotel_name' =>
                        'Ramon Village Resort',

                    'pickup_location' =>
                        'airport',

                    'pickup_at' =>
                        $cancelledPickup,

                    'return_at' =>
                        $cancelledReturn,

                    'flight_number' =>
                        '2M 2065',

                    'special_notes' =>
                        $marker.' Cancelled booking with assignment history.',

                    'total_price' =>
                        190.00,

                    'status' =>
                        'cancelled',
                ],
                [
                    [
                        'cart_type' =>
                            '6_seater',

                        'quantity' =>
                            1,
                    ],
                ],
                [
                    '6_seater' => [
                        'DEMO-6-02',
                    ],
                ]
            );

            /*
             * 8. Pending group request requiring TWO carts.
             */
            $groupPickup = $now
                ->addDays(4)
                ->setTime(8, 0);

            $groupReturn = $now
                ->addDays(4)
                ->setTime(18, 0);

            $createBooking(
                [
                    'customer_id' =>
                        $daniel->id,

                    'full_name' =>
                        $daniel->name,

                    'email' =>
                        $daniel->email,

                    'phone' =>
                        $daniel->phone,

                    'hotel_name' =>
                        'Coco Beach Resort',

                    'pickup_location' =>
                        'water_taxi',

                    'pickup_at' =>
                        $groupPickup,

                    'return_at' =>
                        $groupReturn,

                    'flight_number' =>
                        null,

                    'special_notes' =>
                        $marker.' Group request requiring two 4-seaters.',

                    'total_price' =>
                        0.00,

                    'status' =>
                        'pending',
                ],
                [
                    [
                        'cart_type' =>
                            '4_seater',

                        'quantity' =>
                            2,
                    ],
                ]
            );

            /*
             * Contacts / Leads
             */
            Contact::create([
                'customer_id' =>
                    null,

                'name' =>
                    'Robert King',

                'email' =>
                    'robert.demo@example.com',

                'phone' =>
                    '+5016102001',

                'message' =>
                    $marker.' Asking about a 6-seater for a family vacation.',

                'status' =>
                    'pending',

                'is_read' =>
                    false,
            ]);

            Contact::create([
                'customer_id' =>
                    $emma->id,

                'name' =>
                    $emma->name,

                'email' =>
                    $emma->email,

                'phone' =>
                    $emma->phone,

                'message' =>
                    $marker.' Existing customer asking about extending a rental.',

                'status' =>
                    'working_on_it',

                'is_read' =>
                    true,
            ]);

            Contact::create([
                'customer_id' =>
                    null,

                'name' =>
                    'Olivia Brown',

                'email' =>
                    'olivia.demo@example.com',

                'phone' =>
                    '+5016102003',

                'message' =>
                    $marker.' General airport pickup question.',

                'status' =>
                    'resolved',

                'is_read' =>
                    true,
            ]);

            Contact::create([
                'customer_id' =>
                    null,

                'name' =>
                    'James Wilson',

                'email' =>
                    'james.demo@example.com',

                'phone' =>
                    '+5016102004',

                'message' =>
                    $marker.' Wants two carts for a group.',

                'status' =>
                    'pending',

                'is_read' =>
                    false,
            ]);
        });

        $this->command?->info(
            'Grab One demo data created successfully.'
        );

        $this->command?->info(
            'Business timezone: '.$timezone
        );

        $this->command?->info(
            'Belize now: '.$now->format('Y-m-d H:i')
        );

        $this->command?->info(
            'Demo customers: '.Customer::query()
                ->where(
                    'notes',
                    'like',
                    $marker.'%'
                )
                ->count()
        );

        $this->command?->info(
            'Demo carts: '.Cart::query()
                ->where(
                    'code',
                    'like',
                    'DEMO-%'
                )
                ->count()
        );

        $this->command?->info(
            'Demo bookings: '.Booking::query()
                ->where(
                    'special_notes',
                    'like',
                    $marker.'%'
                )
                ->count()
        );

        $this->command?->info(
            'Demo contacts: '.Contact::query()
                ->where(
                    'message',
                    'like',
                    $marker.'%'
                )
                ->count()
        );
    }
}