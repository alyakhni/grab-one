<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Cart;
use App\Services\BookingCartAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingCartAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingCartAssignmentService $assignments;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assignments =
            app(BookingCartAssignmentService::class);
    }

    public function test_pending_booking_can_be_confirmed_with_exact_available_cart_assignment(): void
    {
        $cart = $this->createCart(
            'GO-101',
            '4_seater'
        );

        $booking = $this->createBooking(
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 1,
                ],
            ]
        );

        $confirmed = $this->assignments->confirm(
            $booking,
            [
                '4_seater' => [
                    $cart->id,
                ],
            ]
        );

        $this->assertSame(
            'confirmed',
            $confirmed->status
        );

        $this->assertDatabaseHas(
            'booking_cart_assignments',
            [
                'booking_item_id' =>
                    $booking->items()->firstOrFail()->id,

                'cart_id' =>
                    $cart->id,
            ]
        );
    }

    public function test_mixed_booking_requires_exact_quantity_for_each_cart_type(): void
    {
        $fourA = $this->createCart(
            'GO-110',
            '4_seater'
        );

        $fourB = $this->createCart(
            'GO-111',
            '4_seater'
        );

        $six = $this->createCart(
            'GO-112',
            '6_seater'
        );

        $booking = $this->createBooking(
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 2,
                ],
                [
                    'cart_type' => '6_seater',
                    'quantity' => 1,
                ],
            ]
        );

        $confirmed = $this->assignments->confirm(
            $booking,
            [
                '4_seater' => [
                    $fourA->id,
                    $fourB->id,
                ],

                '6_seater' => [
                    $six->id,
                ],
            ]
        );

        $this->assertSame(
            'confirmed',
            $confirmed->status
        );

        $this->assertDatabaseCount(
            'booking_cart_assignments',
            3
        );
    }

    public function test_booking_cannot_be_confirmed_with_too_few_carts(): void
    {
        $cart = $this->createCart(
            'GO-120',
            '4_seater'
        );

        $booking = $this->createBooking(
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 2,
                ],
            ]
        );

        try {
            $this->assignments->confirm(
                $booking,
                [
                    '4_seater' => [
                        $cart->id,
                    ],
                ]
            );

            $this->fail(
                'Expected validation exception.'
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'cart_assignments.4_seater',
                $exception->errors()
            );
        }

        $this->assertDatabaseHas(
            'bookings',
            [
                'id' => $booking->id,
                'status' => 'pending',
            ]
        );

        $this->assertDatabaseCount(
            'booking_cart_assignments',
            0
        );
    }

    public function test_wrong_cart_type_is_rejected(): void
    {
        $sixSeater = $this->createCart(
            'GO-130',
            '6_seater'
        );

        $booking = $this->createBooking(
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 1,
                ],
            ]
        );

        try {
            $this->assignments->confirm(
                $booking,
                [
                    '4_seater' => [
                        $sixSeater->id,
                    ],
                ]
            );

            $this->fail(
                'Expected validation exception.'
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'cart_assignments.4_seater',
                $exception->errors()
            );
        }

        $this->assertSame(
            'pending',
            $booking->fresh()->status
        );
    }

    public function test_inactive_or_maintenance_cart_is_rejected(): void
    {
        $cart = $this->createCart(
            'GO-140',
            '4_seater',
            'maintenance'
        );

        $booking = $this->createBooking(
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 1,
                ],
            ]
        );

        try {
            $this->assignments->confirm(
                $booking,
                [
                    '4_seater' => [
                        $cart->id,
                    ],
                ]
            );

            $this->fail(
                'Expected validation exception.'
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'cart_assignments.4_seater',
                $exception->errors()
            );
        }

        $this->assertSame(
            'pending',
            $booking->fresh()->status
        );
    }

    public function test_cart_already_blocked_by_another_confirmed_booking_is_rejected(): void
    {
        $cart = $this->createCart(
            'GO-150',
            '4_seater'
        );

        $existingBooking = $this->createBooking(
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 1,
                ],
            ],
            [
                'status' => 'confirmed',
                'pickup_at' =>
                    '2026-09-10 10:00:00',
                'return_at' =>
                    '2026-09-10 14:00:00',
            ]
        );

        $existingItem =
            $existingBooking
                ->items()
                ->firstOrFail();

        $existingItem
            ->assignments()
            ->create([
                'cart_id' => $cart->id,
            ]);

        $newBooking = $this->createBooking(
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 1,
                ],
            ],
            [
                'pickup_at' =>
                    '2026-09-10 14:30:00',
                'return_at' =>
                    '2026-09-10 18:00:00',
            ]
        );

        try {
            $this->assignments->confirm(
                $newBooking,
                [
                    '4_seater' => [
                        $cart->id,
                    ],
                ]
            );

            $this->fail(
                'Expected validation exception.'
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'cart_assignments.4_seater',
                $exception->errors()
            );
        }

        $this->assertSame(
            'pending',
            $newBooking->fresh()->status
        );
    }

    public function test_confirmed_booking_can_keep_its_own_cart_when_reconfirmed(): void
    {
        $cart = $this->createCart(
            'GO-160',
            '4_seater'
        );

        $booking = $this->createBooking(
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 1,
                ],
            ],
            [
                'status' => 'confirmed',
            ]
        );

        $item =
            $booking
                ->items()
                ->firstOrFail();

        $item
            ->assignments()
            ->create([
                'cart_id' => $cart->id,
            ]);

        $confirmed =
            $this->assignments->confirm(
                $booking,
                [
                    '4_seater' => [
                        $cart->id,
                    ],
                ]
            );

        $this->assertSame(
            'confirmed',
            $confirmed->status
        );

        $this->assertDatabaseCount(
            'booking_cart_assignments',
            1
        );
    }

    public function test_cart_type_not_present_in_booking_is_rejected(): void
    {
        $cart = $this->createCart(
            'GO-170',
            '6_seater'
        );

        $booking = $this->createBooking(
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 1,
                ],
            ]
        );

        try {
            $this->assignments->confirm(
                $booking,
                [
                    '4_seater' => [],
                    '6_seater' => [
                        $cart->id,
                    ],
                ]
            );

            $this->fail(
                'Expected validation exception.'
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'cart_assignments.6_seater',
                $exception->errors()
            );
        }

        $this->assertSame(
            'pending',
            $booking->fresh()->status
        );
    }

    private function createCart(
        string $code,
        string $cartType,
        string $status = 'active'
    ): Cart {
        return Cart::create([
            'code' => $code,
            'cart_type' => $cartType,
            'operational_status' => $status,
        ]);
    }

    private function createBooking(
        array $items,
        array $overrides = []
    ): Booking {
        $booking = Booking::create(
            array_merge(
                [
                    'full_name' =>
                        'Assignment Test Customer',

                    'email' =>
                        'assignment@example.com',

                    'phone' =>
                        '501-555-2000',

                    'pickup_location' =>
                        'hotel',

                    'pickup_at' =>
                        '2026-09-10 10:00:00',

                    'return_at' =>
                        '2026-09-10 14:00:00',

                    'total_price' =>
                        0.00,

                    'status' =>
                        'pending',
                ],
                $overrides
            )
        );

        $booking
            ->items()
            ->createMany(
                $items
            );

        return $booking;
    }
}