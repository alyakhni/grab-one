<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingCartAssignment;
use App\Models\Cart;
use App\Models\Customer;
use App\Services\FleetAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicBookingPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_four_seater_booking_creates_pending_request_without_reserving_fleet(): void
    {
        $fourSeater = Cart::create([
            'code' => 'PRIVATE-4-001',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
            'notes' => 'Private four-seater note.',
        ]);

        $sixSeater = Cart::create([
            'code' => 'PRIVATE-6-001',
            'cart_type' => '6_seater',
            'operational_status' => 'active',
            'notes' => 'Private six-seater note.',
        ]);

        $response = $this
            ->from('/')
            ->post(
                route('booking.store'),
                $this->validPayload()
            );

        $response
            ->assertRedirect('/')
            ->assertSessionHas(
                'success',
                'Your booking request has been submitted! We will contact you shortly to confirm availability, pricing, and details.'
            );

        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('booking_items', 1);
        $this->assertDatabaseCount('booking_cart_assignments', 0);

        $booking = Booking::query()
            ->with(['customer', 'items.assignments'])
            ->sole();

        $this->assertSame('pending', $booking->status);
        $this->assertSame('Public Booking Guest', $booking->full_name);
        $this->assertSame('Public.Guest@example.com', $booking->email);
        $this->assertSame('+501 610-9001', $booking->phone);
        $this->assertSame('0.00', $booking->total_price);
        $this->assertSame(
            '2026-10-20 09:00:00',
            $booking->getRawOriginal('pickup_at')
        );
        $this->assertSame(
            '2026-10-20 13:30:00',
            $booking->getRawOriginal('return_at')
        );
        $this->assertSame(
            '2026-10-20 09:00:00',
            $booking->pickup_at->format('Y-m-d H:i:s')
        );
        $this->assertSame(
            '2026-10-20 13:30:00',
            $booking->return_at->format('Y-m-d H:i:s')
        );
        $this->assertSame(
            'America/Belize',
            $booking->pickup_at->getTimezone()->getName()
        );

        $this->assertNotNull($booking->customer);
        $this->assertSame('Public Booking Guest', $booking->customer->name);
        $this->assertSame(
            'Public.Guest@example.com',
            $booking->customer->email
        );
        $this->assertSame('+501 610-9001', $booking->customer->phone);
        $this->assertSame(
            'public.guest@example.com',
            $booking->customer->email_normalized
        );
        $this->assertSame(
            '+5016109001',
            $booking->customer->phone_normalized
        );

        $item = $booking->items->sole();

        $this->assertSame('4_seater', $item->cart_type);
        $this->assertSame(1, $item->quantity);
        $this->assertCount(0, $item->assignments);

        $this->assertSame('active', $fourSeater->fresh()->operational_status);
        $this->assertSame('active', $sixSeater->fresh()->operational_status);
        $this->assertSame(
            0,
            Cart::query()
                ->where('operational_status', 'reserved')
                ->count()
        );
    }

    public function test_public_six_seater_booking_creates_one_normal_item_without_assignment(): void
    {
        $response = $this
            ->from('/')
            ->post(
                route('booking.store'),
                $this->validPayload([
                    'email' => 'six.seater@example.com',
                    'phone' => '+501 610-9002',
                    'cart_selection' => '6_seater',
                ])
            );

        $response->assertRedirect('/');

        $booking = Booking::query()
            ->with('items.assignments')
            ->sole();

        $this->assertSame('pending', $booking->status);
        $this->assertCount(1, $booking->items);
        $this->assertSame('6_seater', $booking->items->sole()->cart_type);
        $this->assertSame(1, $booking->items->sole()->quantity);
        $this->assertCount(0, $booking->items->sole()->assignments);
        $this->assertDatabaseCount('booking_cart_assignments', 0);
    }

    public function test_public_booking_preserves_quantity_in_one_booking_item(): void
    {
        $this
            ->from('/')
            ->post(
                route('booking.store'),
                $this->validPayload([
                    'email' => 'quantity@example.com',
                    'phone' => '+501 610-9003',
                    'cart_quantities' => [
                        '4_seater' => 2,
                        '6_seater' => 1,
                    ],
                ])
            )
            ->assertRedirect('/');

        $booking = Booking::query()
            ->with('items.assignments')
            ->sole();

        $this->assertSame('pending', $booking->status);
        $this->assertCount(1, $booking->items);
        $this->assertSame('4_seater', $booking->items->sole()->cart_type);
        $this->assertSame(2, $booking->items->sole()->quantity);
        $this->assertCount(0, $booking->items->sole()->assignments);
    }

    public function test_public_mix_booking_is_stored_as_normal_cart_type_items(): void
    {
        $this
            ->from('/')
            ->post(
                route('booking.store'),
                $this->validPayload([
                    'email' => 'mix@example.com',
                    'phone' => '+501 610-9004',
                    'cart_selection' => 'mix',
                ])
            )
            ->assertRedirect('/');

        $booking = Booking::query()
            ->with('items.assignments')
            ->sole();

        $items = $booking->items->keyBy('cart_type');

        $this->assertSame('pending', $booking->status);
        $this->assertCount(2, $items);
        $this->assertSame(1, $items->get('4_seater')->quantity);
        $this->assertSame(1, $items->get('6_seater')->quantity);
        $this->assertFalse($items->has('mix'));
        $this->assertSame(
            0,
            $items->sum(
                fn ($item): int => $item->assignments->count()
            )
        );
        $this->assertDatabaseMissing('booking_items', [
            'cart_type' => 'mix',
        ]);
    }

    public function test_public_booking_reuses_clear_customer_identity_and_preserves_snapshots(): void
    {
        $this
            ->from('/')
            ->post(
                route('booking.store'),
                $this->validPayload([
                    'full_name' => 'First Snapshot Name',
                    'email' => 'Identity.Guest@example.com',
                    'phone' => '610-9005',
                ])
            )
            ->assertRedirect('/');

        $this
            ->from('/')
            ->post(
                route('booking.store'),
                $this->validPayload([
                    'full_name' => 'Second Snapshot Name',
                    'email' => 'identity.guest@example.com',
                    'phone' => '+501 610-9005',
                ])
            )
            ->assertRedirect('/');

        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('bookings', 2);

        $customer = Customer::query()->sole();
        $bookings = Booking::query()->orderBy('id')->get();

        $this->assertSame(
            'identity.guest@example.com',
            $customer->email_normalized
        );
        $this->assertSame('+5016109005', $customer->phone_normalized);
        $this->assertSame('Second Snapshot Name', $customer->name);

        $this->assertSame($customer->id, $bookings[0]->customer_id);
        $this->assertSame($customer->id, $bookings[1]->customer_id);
        $this->assertSame('First Snapshot Name', $bookings[0]->full_name);
        $this->assertSame(
            'Identity.Guest@example.com',
            $bookings[0]->email
        );
        $this->assertSame('610-9005', $bookings[0]->phone);
        $this->assertSame('Second Snapshot Name', $bookings[1]->full_name);
        $this->assertSame(
            'identity.guest@example.com',
            $bookings[1]->email
        );
        $this->assertSame('+501 610-9005', $bookings[1]->phone);
    }

    public function test_public_booking_accepts_request_when_internal_fleet_is_unavailable(): void
    {
        $cart = Cart::create([
            'code' => 'PRIVATE-BUSY-4-001',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        $confirmedBooking = Booking::create([
            'full_name' => 'Existing Confirmed Guest',
            'email' => 'existing.confirmed@example.com',
            'phone' => '+5016109999',
            'pickup_location' => 'hotel',
            'pickup_at' => '2026-10-20 08:00:00',
            'return_at' => '2026-10-20 15:00:00',
            'total_price' => 150.00,
            'status' => 'confirmed',
        ]);

        $confirmedItem = $confirmedBooking->items()->create([
            'cart_type' => '4_seater',
            'quantity' => 1,
        ]);

        $confirmedItem->assignments()->create([
            'cart_id' => $cart->id,
        ]);

        $this->assertSame(
            0,
            app(FleetAvailabilityService::class)->availableCount(
                '4_seater',
                '2026-10-20 09:00:00',
                '2026-10-20 13:30:00'
            )
        );

        $this
            ->from('/')
            ->post(
                route('booking.store'),
                $this->validPayload([
                    'email' => 'pending.despite.stock@example.com',
                    'phone' => '+501 610-9006',
                ])
            )
            ->assertRedirect('/');

        $publicBooking = Booking::query()
            ->where('email', 'pending.despite.stock@example.com')
            ->with('items.assignments')
            ->sole();

        $this->assertSame('pending', $publicBooking->status);
        $this->assertSame(
            0,
            $publicBooking->items->sum(
                fn ($item): int => $item->assignments->count()
            )
        );
        $this->assertSame(1, BookingCartAssignment::query()->count());
        $this->assertSame('active', $cart->fresh()->operational_status);
    }

    public function test_public_booking_pages_and_submission_do_not_expose_internal_fleet_state(): void
    {
        $fourSeater = new Cart([
            'code' => 'PRIVATE-4-001',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
            'notes' => 'PRIVATE ADMIN NOTE 4',
        ]);
        $fourSeater->id = 987654321;
        $fourSeater->save();

        $sixSeater = new Cart([
            'code' => 'PRIVATE-6-001',
            'cart_type' => '6_seater',
            'operational_status' => 'maintenance',
            'notes' => 'PRIVATE ADMIN NOTE 6',
        ]);
        $sixSeater->id = 987654322;
        $sixSeater->save();

        $this->assertPublicPrivacyBoundary(
            $this->get('/')->assertOk()
        );

        $response = $this
            ->followingRedirects()
            ->from('/')
            ->post(
                route('booking.store'),
                $this->validPayload([
                    'email' => 'privacy.booking@example.com',
                    'phone' => '+501 610-9007',
                ])
            )
            ->assertOk()
            ->assertSee('4-Seater Cart')
            ->assertSee('6-Seater Cart');

        $this->assertPublicPrivacyBoundary($response);

        $booking = Booking::query()->sole();

        $this->assertSame('pending', $booking->status);
        $this->assertDatabaseCount('booking_cart_assignments', 0);
    }

    #[DataProvider('invalidBookingPayloads')]
    public function test_invalid_public_booking_does_not_create_partial_state(
        array $overrides,
        array $expectedErrors
    ): void {
        $this
            ->from('/')
            ->post(
                route('booking.store'),
                $this->validPayload($overrides)
            )
            ->assertRedirect('/')
            ->assertSessionHasErrors($expectedErrors);

        $this->assertDatabaseCount('customers', 0);
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booking_items', 0);
        $this->assertDatabaseCount('booking_cart_assignments', 0);
    }

    public static function invalidBookingPayloads(): array
    {
        return [
            'return is not after pickup' => [
                [
                    'return_at' => '2026-10-20T09:00',
                ],
                ['return_at'],
            ],
            'unsupported cart selection' => [
                [
                    'cart_selection' => 'private_physical_cart',
                ],
                ['cart_selection'],
            ],
            'quantity below minimum' => [
                [
                    'cart_quantities' => [
                        '4_seater' => 0,
                        '6_seater' => 1,
                    ],
                ],
                ['cart_quantities.4_seater'],
            ],
        ];
    }

    private function validPayload(array $overrides = []): array
    {
        return array_replace_recursive(
            [
                'full_name' => 'Public Booking Guest',
                'email' => 'Public.Guest@example.com',
                'phone' => '+501 610-9001',
                'hotel_name' => 'Seaside Hotel',
                'pickup_location' => 'hotel',
                'pickup_at' => '2026-10-20T09:00',
                'return_at' => '2026-10-20T13:30',
                'cart_selection' => '4_seater',
                'cart_quantities' => [
                    '4_seater' => 1,
                    '6_seater' => 1,
                ],
                'special_notes' => 'Public booking regression coverage.',
            ],
            $overrides
        );
    }

    private function assertPublicPrivacyBoundary(TestResponse $response): void
    {
        foreach (
            [
                'PRIVATE-4-001',
                'PRIVATE-6-001',
                '987654321',
                '987654322',
                'PRIVATE ADMIN NOTE 4',
                'PRIVATE ADMIN NOTE 6',
                'booking_cart_assignments',
                'cart_assignments',
                'operational_status',
                'Operationally free',
                'Available now',
                'Identity Review',
                'Customer operational history',
            ] as $privateValue
        ) {
            $response->assertDontSee($privateValue);
        }
    }
}
