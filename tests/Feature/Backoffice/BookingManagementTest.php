<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Bookings\CreateBooking;
use App\Livewire\Backoffice\Bookings\EditBooking;
use App\Livewire\Backoffice\Bookings\Index;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\User;
use App\Services\FleetAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_bookings(): void
    {
        $this->get(route('admin.bookings.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_bookings(): void
    {
        $user = User::factory()->create();

        $booking = $this->createBooking([
            'full_name' => 'Maria Johnson',
        ]);

        $this->actingAs($user)
            ->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertSee($booking->full_name)
            ->assertSee('4-Seater Cart');
    }

    public function test_bookings_can_be_searched(): void
    {
        $user = User::factory()->create();

        $this->createBooking([
            'full_name' => 'Alice Traveler',
        ]);

        $this->createBooking([
            'full_name' => 'Bob Traveler',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('search', 'Alice')
            ->assertSee('Alice Traveler')
            ->assertDontSee('Bob Traveler');
    }

    public function test_bookings_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();

        $this->createBooking([
            'full_name' => 'Pending Guest',
            'status' => 'pending',
        ]);

        $this->createBooking([
            'full_name' => 'Confirmed Guest',
            'status' => 'confirmed',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('status', 'confirmed')
            ->assertSee('Confirmed Guest')
            ->assertDontSee('Pending Guest');
    }

    public function test_authenticated_user_can_create_booking(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateBooking::class)
            ->set('full_name', 'New Customer')
            ->set('email', 'new@example.com')
            ->set('phone', '501-555-0100')
            ->set('hotel_name', 'Sunset Hotel')
            ->set('pickup_location', 'hotel')
            ->set('pickup_at', '2026-09-10T10:00')
            ->set('return_at', '2026-09-12T10:00')
            ->set('cart_selection', '4_seater')
            ->set('cart_quantities.4_seater', 2)
            ->set('special_notes', 'Test booking')
            ->set('flight_number', 'AA123')
            ->set('total_price', '180.00')
            ->set('status', 'pending')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'full_name' => 'New Customer',
            'email' => 'new@example.com',
            'pickup_location' => 'hotel',
            'status' => 'pending',
            'total_price' => 180.00,
        ]);

        $this->assertDatabaseHas('booking_items', [
            'cart_type' => '4_seater',
            'quantity' => 2,
        ]);
    }

    public function test_authenticated_user_can_create_mixed_booking(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateBooking::class)
            ->set('full_name', 'Mixed Cart Customer')
            ->set('email', 'mixed@example.com')
            ->set('phone', '501-555-0110')
            ->set('pickup_location', 'airport')
            ->set('pickup_at', '2026-09-15T09:30')
            ->set('return_at', '2026-09-18T16:00')
            ->set('cart_selection', 'mix')
            ->set('cart_quantities.4_seater', 2)
            ->set('cart_quantities.6_seater', 1)
            ->set('total_price', '0.00')
            ->set('status', 'pending')
            ->call('save')
            ->assertHasNoErrors();

        $booking = Booking::query()
            ->where('email', 'mixed@example.com')
            ->firstOrFail();

        $this->assertDatabaseHas('booking_items', [
            'booking_id' => $booking->id,
            'cart_type' => '4_seater',
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('booking_items', [
            'booking_id' => $booking->id,
            'cart_type' => '6_seater',
            'quantity' => 1,
        ]);
    }

    public function test_admin_cannot_create_booking_directly_as_confirmed(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateBooking::class)
            ->set('full_name', 'Bypass Attempt')
            ->set('email', 'bypass@example.com')
            ->set('phone', '501-555-0999')
            ->set('pickup_location', 'hotel')
            ->set('pickup_at', '2026-09-10T10:00')
            ->set('return_at', '2026-09-12T10:00')
            ->set('cart_selection', '4_seater')
            ->set('cart_quantities.4_seater', 1)
            ->set('total_price', '100.00')
            ->set('status', 'confirmed')
            ->call('save')
            ->assertHasErrors(['status']);

        $this->assertDatabaseMissing('bookings', [
            'email' => 'bypass@example.com',
        ]);
    }

    public function test_authenticated_user_can_edit_booking_without_confirming(): void
    {
        $user = User::factory()->create();

        $booking = $this->createBooking();

        Livewire::actingAs($user)
            ->test(EditBooking::class, [
                'booking' => $booking,
            ])
            ->set('full_name', 'Updated Customer')
            ->set('status', 'pending')
            ->set('cart_selection', '6_seater')
            ->set('cart_quantities.6_seater', 3)
            ->set('total_price', '250.00')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'full_name' => 'Updated Customer',
            'status' => 'pending',
            'total_price' => 250.00,
        ]);

        $this->assertDatabaseHas('booking_items', [
            'booking_id' => $booking->id,
            'cart_type' => '6_seater',
            'quantity' => 3,
        ]);

        $this->assertDatabaseMissing('booking_items', [
            'booking_id' => $booking->id,
            'cart_type' => '4_seater',
        ]);
    }

    public function test_booking_cannot_be_confirmed_without_exact_assignment(): void
    {
        $user = User::factory()->create();

        $booking = $this->createBooking();

        Livewire::actingAs($user)
            ->test(EditBooking::class, [
                'booking' => $booking,
            ])
            ->set('status', 'confirmed')
            ->call('save')
            ->assertHasErrors([
                'cart_assignments.4_seater',
            ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount(
            'booking_cart_assignments',
            0
        );
    }

    public function test_admin_can_confirm_booking_with_available_cart(): void
    {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-201',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        $booking = $this->createBooking();

        Livewire::actingAs($user)
            ->test(EditBooking::class, [
                'booking' => $booking,
            ])
            ->set('status', 'confirmed')
            ->set(
                'cart_assignments.4_seater',
                [
                    (string) $cart->id,
                ]
            )
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(
                route('admin.bookings.index')
            );

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas(
            'booking_cart_assignments',
            [
                'booking_item_id' => $booking
                    ->items()
                    ->firstOrFail()
                    ->id,

                'cart_id' => $cart->id,
            ]
        );
    }

    public function test_admin_cannot_confirm_booking_with_conflicting_cart(): void
    {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-202',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        $existing = $this->createBooking(
            [
                'status' => 'confirmed',
            ]
        );

        $existing
            ->items()
            ->firstOrFail()
            ->assignments()
            ->create([
                'cart_id' => $cart->id,
            ]);

        $booking = $this->createBooking([
            'email' => 'conflict@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(EditBooking::class, [
                'booking' => $booking,
            ])
            ->set('status', 'confirmed')
            ->set(
                'cart_assignments.4_seater',
                [
                    (string) $cart->id,
                ]
            )
            ->call('save')
            ->assertHasErrors([
                'cart_assignments.4_seater',
            ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'pending',
        ]);
    }

    public function test_available_cart_is_visible_when_confirming_booking(): void
    {
        $user = User::factory()->create();

        Cart::create([
            'code' => 'GO-203',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        Cart::create([
            'code' => 'GO-204',
            'cart_type' => '4_seater',
            'operational_status' => 'maintenance',
        ]);

        $booking = $this->createBooking();

        Livewire::actingAs($user)
            ->test(EditBooking::class, [
                'booking' => $booking,
            ])
            ->set('status', 'confirmed')
            ->assertSee('GO-203')
            ->assertDontSee('GO-204');
    }

    public function test_reopening_confirmed_booking_as_pending_clears_assignment_and_preserves_booking_details(): void
    {
        $user = User::factory()->create();

        $customer = Customer::create([
            'name' => 'Original Customer',
            'email' => 'original@example.com',
            'email_normalized' => 'original@example.com',
            'phone' => '+5016105100',
            'phone_normalized' => '+5016105100',
        ]);

        $cart = Cart::create([
            'code' => 'GO-205',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        $booking = $this->createBooking([
            'customer_id' => $customer->id,
            'full_name' => 'Original Customer',
            'email' => 'original@example.com',
            'phone' => '+5016105100',
            'pickup_at' => '2026-09-20 09:00:00',
            'return_at' => '2026-09-20 17:00:00',
            'total_price' => 325.00,
            'status' => 'confirmed',
        ]);

        $item = $booking
            ->items()
            ->firstOrFail();

        $item
            ->assignments()
            ->create([
                'cart_id' => $cart->id,
            ]);

        Livewire::actingAs($user)
            ->test(EditBooking::class, [
                'booking' => $booking,
            ])
            ->set('status', 'pending')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(
                route('admin.bookings.index')
            );

        $booking->refresh();

        $this->assertSame(
            'pending',
            $booking->status
        );

        $this->assertSame(
            $customer->id,
            $booking->customer_id
        );

        $this->assertSame(
            'Original Customer',
            $booking->full_name
        );

        $this->assertSame(
            'original@example.com',
            $booking->email
        );

        $this->assertSame(
            '+5016105100',
            $booking->phone
        );

        $this->assertSame(
            '2026-09-20 09:00:00',
            $booking->pickup_at
                ->format('Y-m-d H:i:s')
        );

        $this->assertSame(
            '2026-09-20 17:00:00',
            $booking->return_at
                ->format('Y-m-d H:i:s')
        );

        $this->assertSame(
            '325.00',
            $booking->total_price
        );

        $this->assertDatabaseHas(
            'booking_items',
            [
                'id' => $item->id,
                'booking_id' => $booking->id,
                'cart_type' => '4_seater',
                'quantity' => 1,
            ]
        );

        $this->assertDatabaseCount(
            'booking_cart_assignments',
            0
        );

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'code' => 'GO-205',
        ]);
    }

    public function test_reopening_confirmed_booking_as_pending_releases_cart_for_availability(): void
    {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-206',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        $booking = $this->createBooking([
            'status' => 'confirmed',
        ]);

        $booking
            ->items()
            ->firstOrFail()
            ->assignments()
            ->create([
                'cart_id' => $cart->id,
            ]);

        $availability = app(
            FleetAvailabilityService::class
        );

        $this->assertFalse(
            $availability->isCartAvailable(
                $cart,
                $booking->pickup_at,
                $booking->return_at
            )
        );

        Livewire::actingAs($user)
            ->test(EditBooking::class, [
                'booking' => $booking,
            ])
            ->set('status', 'pending')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(
            $availability->isCartAvailable(
                $cart,
                $booking->pickup_at,
                $booking->return_at
            )
        );

        $this->assertDatabaseMissing(
            'booking_cart_assignments',
            [
                'cart_id' => $cart->id,
            ]
        );
    }

    #[DataProvider('historicalStatuses')]
    public function test_historical_status_transition_preserves_assignment_history_and_releases_cart(
        string $historicalStatus
    ): void {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-207',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        $booking = $this->createBooking([
            'status' => 'confirmed',
        ]);

        $item = $booking
            ->items()
            ->firstOrFail();

        $item
            ->assignments()
            ->create([
                'cart_id' => $cart->id,
            ]);

        $availability = app(
            FleetAvailabilityService::class
        );

        $this->assertFalse(
            $availability->isCartAvailable(
                $cart,
                $booking->pickup_at,
                $booking->return_at
            )
        );

        Livewire::actingAs($user)
            ->test(EditBooking::class, [
                'booking' => $booking,
            ])
            ->set('status', $historicalStatus)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(
                route('admin.bookings.index')
            );

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => $historicalStatus,
        ]);

        $this->assertDatabaseHas(
            'booking_items',
            [
                'id' => $item->id,
                'booking_id' => $booking->id,
                'cart_type' => '4_seater',
                'quantity' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'booking_cart_assignments',
            [
                'booking_item_id' => $item->id,

                'cart_id' => $cart->id,
            ]
        );

        $this->assertTrue(
            $availability->isCartAvailable(
                $cart,
                $booking->pickup_at,
                $booking->return_at
            )
        );
    }

    public function test_historical_status_change_cannot_replace_assigned_cart_composition(): void
    {
        $user = User::factory()->create();

        $booking = Booking::create([
            'full_name' => 'History Protection',
            'email' => 'history.protection@example.com',
            'phone' => '+5016109999',
            'hotel_name' => 'Demo Hotel',
            'pickup_location' => 'hotel',
            'pickup_at' => '2026-08-25 09:00:00',
            'return_at' => '2026-08-25 17:00:00',
            'total_price' => 150.00,
            'status' => 'confirmed',
        ]);

        $item = $booking
            ->items()
            ->create([
                'cart_type' => '4_seater',
                'quantity' => 1,
            ]);

        $cart = Cart::create([
            'code' => 'HISTORY-001',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
            'notes' => 'History protection test cart.',
        ]);

        $item
            ->assignments()
            ->create([
                'cart_id' => $cart->id,
            ]);

        Livewire::actingAs($user)
            ->test(
                EditBooking::class,
                [
                    'booking' => $booking,
                ]
            )
            ->set('status', 'cancelled')
            ->set('cart_selection', '6_seater')
            ->set('cart_quantities.6_seater', 1)
            ->call('save')
            ->assertHasErrors([
                'cart_selection',
            ]);

        $this->assertDatabaseHas(
            'bookings',
            [
                'id' => $booking->id,
                'status' => 'confirmed',
            ]
        );

        $this->assertDatabaseHas(
            'booking_items',
            [
                'id' => $item->id,
                'booking_id' => $booking->id,
                'cart_type' => '4_seater',
                'quantity' => 1,
            ]
        );

        $this->assertDatabaseMissing(
            'booking_items',
            [
                'booking_id' => $booking->id,
                'cart_type' => '6_seater',
            ]
        );

        $this->assertDatabaseHas(
            'booking_cart_assignments',
            [
                'booking_item_id' => $item->id,
                'cart_id' => $cart->id,
            ]
        );
    }

    public function test_authenticated_user_can_delete_booking(): void
    {
        $user = User::factory()->create();

        $booking = $this->createBooking();

        Livewire::actingAs($user)
            ->test(EditBooking::class, [
                'booking' => $booking,
            ])
            ->call('delete')
            ->assertRedirect(
                route('admin.bookings.index')
            );

        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
        ]);

        $this->assertDatabaseMissing(
            'booking_items',
            [
                'booking_id' => $booking->id,
            ]
        );
    }

    public function test_authenticated_user_can_bulk_delete_bookings(): void
    {
        $user = User::factory()->create();

        $first = $this->createBooking([
            'full_name' => 'First Guest',
        ]);

        $second = $this->createBooking([
            'full_name' => 'Second Guest',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('selected', [
                (string) $first->id,
                (string) $second->id,
            ])
            ->call('deleteSelected');

        $this->assertDatabaseMissing(
            'bookings',
            [
                'id' => $first->id,
            ]
        );

        $this->assertDatabaseMissing(
            'bookings',
            [
                'id' => $second->id,
            ]
        );
    }

    public static function historicalStatuses(): array
    {
        return [
            'completed' => [
                'completed',
            ],
            'cancelled' => [
                'cancelled',
            ],
        ];
    }

    private function createBooking(
        array $overrides = [],
        array $items = []
    ): Booking {
        $booking = Booking::create(
            array_merge(
                [
                    'full_name' => 'Test Customer',

                    'email' => 'customer@example.com',

                    'phone' => '501-555-0000',

                    'hotel_name' => 'Test Hotel',

                    'pickup_location' => 'hotel',

                    'pickup_at' => '2026-09-10 10:00:00',

                    'return_at' => '2026-09-12 10:00:00',

                    'special_notes' => null,

                    'flight_number' => null,

                    'total_price' => 180.00,

                    'status' => 'pending',
                ],
                $overrides
            )
        );

        if ($items === []) {
            $items = [
                [
                    'cart_type' => '4_seater',

                    'quantity' => 1,
                ],
            ];
        }

        $booking
            ->items()
            ->createMany(
                $items
            );

        return $booking;
    }
}
