<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Bookings\CreateBooking;
use App\Livewire\Backoffice\Bookings\EditBooking;
use App\Livewire\Backoffice\Bookings\Index;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_bookings(): void
    {
        $this->get(route('backoffice.bookings.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_bookings(): void
    {
        $user = User::factory()->create();

        $booking = $this->createBooking([
            'full_name' => 'Maria Johnson',
        ]);

        $this->actingAs($user)
            ->get(route('backoffice.bookings.index'))
            ->assertOk()
            ->assertSee($booking->full_name);
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
            ->set('pickup_location', 'My Hotel')
            ->set('pickup_date', '2026-09-10T10:00')
            ->set('return_date', '2026-09-12T10:00')
            ->set('cart_type', '4-Seater')
            ->set('special_notes', 'Test booking')
            ->set('flight_number', 'AA123')
            ->set('total_days', 2)
            ->set('total_price', '180.00')
            ->set('status', 'pending')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('backoffice.bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'full_name' => 'New Customer',
            'email' => 'new@example.com',
            'status' => 'pending',
            'total_price' => 180.00,
        ]);
    }

    public function test_authenticated_user_can_edit_booking(): void
    {
        $user = User::factory()->create();
        $booking = $this->createBooking();

        Livewire::actingAs($user)
            ->test(EditBooking::class, ['booking' => $booking])
            ->set('full_name', 'Updated Customer')
            ->set('status', 'confirmed')
            ->set('total_price', '250.00')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('backoffice.bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'full_name' => 'Updated Customer',
            'status' => 'confirmed',
            'total_price' => 250.00,
        ]);
    }

    public function test_authenticated_user_can_delete_booking(): void
    {
        $user = User::factory()->create();
        $booking = $this->createBooking();

        Livewire::actingAs($user)
            ->test(EditBooking::class, ['booking' => $booking])
            ->call('delete')
            ->assertRedirect(route('backoffice.bookings.index'));

        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
        ]);
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

        $this->assertDatabaseMissing('bookings', ['id' => $first->id]);
        $this->assertDatabaseMissing('bookings', ['id' => $second->id]);
    }

    private function createBooking(array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '501-555-0000',
            'hotel_name' => 'Test Hotel',
            'pickup_location' => 'My Hotel',
            'pickup_date' => '2026-09-10 10:00:00',
            'return_date' => '2026-09-12 10:00:00',
            'cart_type' => '4-Seater',
            'special_notes' => null,
            'flight_number' => null,
            'total_days' => 2,
            'total_price' => 180.00,
            'status' => 'pending',
        ], $overrides));
    }
}