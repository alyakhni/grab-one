<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Cart;
use App\Services\FleetAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class FleetAvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private FleetAvailabilityService $availability;

    protected function setUp(): void
    {
        parent::setUp();

        $this->availability =
            app(FleetAvailabilityService::class);
    }

    public function test_active_unassigned_cart_is_available(): void
    {
        $cart = $this->createCart(
            'GO-001',
            '4_seater'
        );

        $this->assertTrue(
            $this->availability->isCartAvailable(
                $cart,
                '2026-09-10 10:00:00',
                '2026-09-10 14:00:00'
            )
        );
    }

    public function test_non_active_cart_is_not_available(): void
    {
        $maintenance = $this->createCart(
            'GO-002',
            '4_seater',
            'maintenance'
        );

        $inactive = $this->createCart(
            'GO-003',
            '4_seater',
            'inactive'
        );

        $this->assertFalse(
            $this->availability->isCartAvailable(
                $maintenance,
                '2026-09-10 10:00:00',
                '2026-09-10 14:00:00'
            )
        );

        $this->assertFalse(
            $this->availability->isCartAvailable(
                $inactive,
                '2026-09-10 10:00:00',
                '2026-09-10 14:00:00'
            )
        );
    }

    public function test_overlapping_confirmed_booking_blocks_cart(): void
    {
        $cart = $this->createCart(
            'GO-010',
            '4_seater'
        );

        $this->assignBooking(
            $cart,
            'confirmed',
            '2026-09-10 10:00:00',
            '2026-09-10 14:00:00'
        );

        $this->assertFalse(
            $this->availability->isCartAvailable(
                $cart,
                '2026-09-10 11:00:00',
                '2026-09-10 12:00:00'
            )
        );
    }

    public function test_pending_booking_does_not_block_cart(): void
    {
        $cart = $this->createCart(
            'GO-020',
            '4_seater'
        );

        $this->assignBooking(
            $cart,
            'pending',
            '2026-09-10 10:00:00',
            '2026-09-10 14:00:00'
        );

        $this->assertTrue(
            $this->availability->isCartAvailable(
                $cart,
                '2026-09-10 11:00:00',
                '2026-09-10 12:00:00'
            )
        );
    }

    public function test_cancelled_and_completed_bookings_do_not_block(): void
    {
        $cancelledCart = $this->createCart(
            'GO-030',
            '4_seater'
        );

        $completedCart = $this->createCart(
            'GO-031',
            '4_seater'
        );

        $this->assignBooking(
            $cancelledCart,
            'cancelled',
            '2026-09-10 10:00:00',
            '2026-09-10 14:00:00'
        );

        $this->assignBooking(
            $completedCart,
            'completed',
            '2026-09-10 10:00:00',
            '2026-09-10 14:00:00'
        );

        $this->assertTrue(
            $this->availability->isCartAvailable(
                $cancelledCart,
                '2026-09-10 11:00:00',
                '2026-09-10 12:00:00'
            )
        );

        $this->assertTrue(
            $this->availability->isCartAvailable(
                $completedCart,
                '2026-09-10 11:00:00',
                '2026-09-10 12:00:00'
            )
        );
    }

    public function test_return_buffer_blocks_cart_until_buffer_ends(): void
    {
        $cart = $this->createCart(
            'GO-040',
            '6_seater'
        );

        $this->assignBooking(
            $cart,
            'confirmed',
            '2026-09-10 10:00:00',
            '2026-09-10 14:00:00'
        );

        $this->assertFalse(
            $this->availability->isCartAvailable(
                $cart,
                '2026-09-10 14:30:00',
                '2026-09-10 16:00:00'
            )
        );
    }

    public function test_cart_is_available_at_exact_buffer_boundary(): void
    {
        $cart = $this->createCart(
            'GO-050',
            '6_seater'
        );

        $this->assignBooking(
            $cart,
            'confirmed',
            '2026-09-10 10:00:00',
            '2026-09-10 14:00:00'
        );

        $this->assertTrue(
            $this->availability->isCartAvailable(
                $cart,
                '2026-09-10 15:00:00',
                '2026-09-10 17:00:00'
            )
        );
    }

    public function test_requested_booking_buffer_is_checked_against_next_booking(): void
    {
        $cart = $this->createCart(
            'GO-060',
            '6_seater'
        );

        $this->assignBooking(
            $cart,
            'confirmed',
            '2026-09-10 15:00:00',
            '2026-09-10 18:00:00'
        );

        $this->assertFalse(
            $this->availability->isCartAvailable(
                $cart,
                '2026-09-10 12:00:00',
                '2026-09-10 14:30:00'
            )
        );
    }

    public function test_available_carts_returns_only_requested_active_type(): void
    {
        $fourSeater = $this->createCart(
            'GO-070',
            '4_seater'
        );

        $this->createCart(
            'GO-071',
            '6_seater'
        );

        $this->createCart(
            'GO-072',
            '4_seater',
            'maintenance'
        );

        $available = $this->availability
            ->availableCarts(
                '4_seater',
                '2026-09-10 10:00:00',
                '2026-09-10 14:00:00'
            );

        $this->assertCount(
            1,
            $available
        );

        $this->assertSame(
            $fourSeater->id,
            $available->first()->id
        );
    }

    public function test_invalid_time_window_is_rejected(): void
    {
        $cart = $this->createCart(
            'GO-080',
            '4_seater'
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this->availability->isCartAvailable(
            $cart,
            '2026-09-10 14:00:00',
            '2026-09-10 10:00:00'
        );
    }

    public function test_current_booking_can_be_excluded_from_availability_check(): void
    {
        $cart = $this->createCart(
            'GO-090',
            '4_seater'
        );

        $booking = $this->assignBooking(
            $cart,
            'confirmed',
            '2026-09-10 10:00:00',
            '2026-09-10 14:00:00'
        );

        $this->assertFalse(
            $this->availability->isCartAvailable(
                $cart,
                '2026-09-10 11:00:00',
                '2026-09-10 12:00:00'
            )
        );

        $this->assertTrue(
            $this->availability->isCartAvailable(
                $cart,
                '2026-09-10 11:00:00',
                '2026-09-10 12:00:00',
                $booking->id
            )
        );
    }

    public function test_excluding_current_booking_does_not_hide_other_conflicts(): void
    {
        $cart = $this->createCart(
            'GO-091',
            '4_seater'
        );

        $currentBooking = $this->assignBooking(
            $cart,
            'confirmed',
            '2026-09-10 10:00:00',
            '2026-09-10 14:00:00'
        );

        $this->assignBooking(
            $cart,
            'confirmed',
            '2026-09-10 11:00:00',
            '2026-09-10 13:00:00'
        );

        $this->assertFalse(
            $this->availability->isCartAvailable(
                $cart,
                '2026-09-10 11:30:00',
                '2026-09-10 12:30:00',
                $currentBooking->id
            )
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

    private function assignBooking(
        Cart $cart,
        string $status,
        string $pickupAt,
        string $returnAt
    ): Booking {
        $booking = Booking::create([
            'full_name' => 'Availability Test Customer',
            'email' => 'availability@example.com',
            'phone' => '501-555-1000',
            'pickup_location' => 'hotel',
            'pickup_at' => $pickupAt,
            'return_at' => $returnAt,
            'total_price' => 0.00,
            'status' => $status,
        ]);

        $item = $booking->items()->create([
            'cart_type' => $cart->cart_type,
            'quantity' => 1,
        ]);

        $item->assignments()->create([
            'cart_id' => $cart->id,
        ]);

        return $booking;
    }
}