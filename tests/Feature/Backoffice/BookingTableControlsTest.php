<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Bookings\Index;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookingTableControlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_date_filter_includes_bookings_that_overlap_selected_days(): void
    {
        $user = User::factory()->create();

        $overlapping = $this->createBooking(
            'Overlapping Guest',
            [
                'pickup_at' => '2026-08-24 10:00:00',
                'return_at' => '2026-08-26 10:00:00',
            ]
        );

        $outside = $this->createBooking(
            'Outside Guest',
            [
                'pickup_at' => '2026-08-20 10:00:00',
                'return_at' => '2026-08-20 18:00:00',
            ]
        );

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openFilters')
            ->set('draftDateFrom', '2026-08-25')
            ->set('draftDateTo', '2026-08-25')
            ->call('applyFilters')
            ->assertSee($overlapping->full_name)
            ->assertDontSee($outside->full_name)
            ->assertSet('dateFrom', '2026-08-25')
            ->assertSet('dateTo', '2026-08-25')
            ->assertSet('filtersOpen', false);
    }

    public function test_booking_filters_can_be_combined_and_cleared(): void
    {
        $user = User::factory()->create();

        $matching = $this->createBooking(
            'Matching Guest',
            [
                'status' => 'confirmed',
                'pickup_location' => 'airport',
            ],
            [
                [
                    'cart_type' => '6_seater',
                    'quantity' => 1,
                ],
            ]
        );

        $wrongStatus = $this->createBooking(
            'Wrong Status Guest',
            [
                'status' => 'pending',
                'pickup_location' => 'airport',
            ],
            [
                [
                    'cart_type' => '6_seater',
                    'quantity' => 1,
                ],
            ]
        );

        $wrongType = $this->createBooking(
            'Wrong Type Guest',
            [
                'status' => 'confirmed',
                'pickup_location' => 'airport',
            ],
            [
                [
                    'cart_type' => '4_seater',
                    'quantity' => 1,
                ],
            ]
        );

        $component = Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openFilters')
            ->set('draftStatus', 'confirmed')
            ->set('draftCartType', '6_seater')
            ->set('draftPickupLocation', 'airport')
            ->call('applyFilters')
            ->assertSee($matching->full_name)
            ->assertDontSee($wrongStatus->full_name)
            ->assertDontSee($wrongType->full_name);

        $component
            ->call('clearFilters')
            ->assertSee($matching->full_name)
            ->assertSee($wrongStatus->full_name)
            ->assertSee($wrongType->full_name)
            ->assertSet('status', '')
            ->assertSet('cartType', '')
            ->assertSet('pickupLocation', '')
            ->assertSet('dateFrom', '')
            ->assertSet('dateTo', '');
    }

    public function test_booking_date_filter_rejects_reversed_range(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openFilters')
            ->set('draftDateFrom', '2026-08-26')
            ->set('draftDateTo', '2026-08-25')
            ->call('applyFilters')
            ->assertHasErrors([
                'draftDateTo',
            ])
            ->assertSet('filtersOpen', true);
    }

    public function test_booking_pickup_sort_toggles_between_ascending_and_descending(): void
    {
        $user = User::factory()->create();

        $early = $this->createBooking(
            'Early Pickup',
            [
                'pickup_at' => '2026-08-24 08:00:00',
                'return_at' => '2026-08-24 12:00:00',
            ]
        );

        $late = $this->createBooking(
            'Late Pickup',
            [
                'pickup_at' => '2026-08-24 16:00:00',
                'return_at' => '2026-08-24 20:00:00',
            ]
        );

        $component = Livewire::actingAs($user)
            ->test(Index::class)
            ->call('sortBy', 'pickup_at')
            ->assertSet('sortField', 'pickup_at')
            ->assertSet('sortDirection', 'asc')
            ->assertSeeInOrder([
                $early->full_name,
                $late->full_name,
            ]);

        $component
            ->call('sortBy', 'pickup_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSeeInOrder([
                $late->full_name,
                $early->full_name,
            ]);
    }

    public function test_unsortable_booking_field_is_ignored(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('sortBy', 'special_notes')
            ->assertSet('sortField', 'created_at')
            ->assertSet('sortDirection', 'desc');
    }

    private function createBooking(
        string $name,
        array $overrides = [],
        array $items = []
    ): Booking {
        $booking = Booking::create(
            array_merge(
                [
                    'full_name' => $name,
                    'email' => strtolower(
                        str_replace(
                            ' ',
                            '.',
                            $name
                        )
                    ).'@example.com',
                    'phone' => '501-555-0000',
                    'hotel_name' => 'Test Hotel',
                    'pickup_location' => 'hotel',
                    'pickup_at' => '2026-08-24 10:00:00',
                    'return_at' => '2026-08-24 18:00:00',
                    'special_notes' => null,
                    'flight_number' => null,
                    'total_price' => 100.00,
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
