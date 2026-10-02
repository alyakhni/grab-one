<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Carts\CreateCart;
use App\Livewire\Backoffice\Carts\EditCart;
use App\Livewire\Backoffice\Carts\Index;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_fleet(): void
    {
        $this->get(route('admin.carts.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_fleet(): void
    {
        $user = User::factory()->create();

        Cart::create([
            'code' => 'GO-001',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('admin.carts.index'))
            ->assertOk()
            ->assertSee('GO-001')
            ->assertSee('4-Seater Cart')
            ->assertSee('Active');
    }

    public function test_carts_can_be_searched(): void
    {
        $user = User::factory()->create();

        Cart::create([
            'code' => 'GO-001',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        Cart::create([
            'code' => 'GO-002',
            'cart_type' => '6_seater',
            'operational_status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('search', 'GO-001')
            ->assertSee('GO-001')
            ->assertDontSee('GO-002');
    }

    public function test_carts_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();

        Cart::create([
            'code' => 'GO-001',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        Cart::create([
            'code' => 'GO-002',
            'cart_type' => '4_seater',
            'operational_status' => 'maintenance',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('status', 'maintenance')
            ->assertSee('GO-002')
            ->assertDontSee('GO-001');
    }

    public function test_authenticated_user_can_create_cart(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateCart::class)
            ->set('code', 'go-010')
            ->set('cart_type', '6_seater')
            ->set('operational_status', 'active')
            ->set('notes', 'Family cart')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.carts.index'));

        $this->assertDatabaseHas('carts', [
            'code' => 'GO-010',
            'cart_type' => '6_seater',
            'operational_status' => 'active',
            'notes' => 'Family cart',
        ]);
    }

    public function test_authenticated_user_can_edit_cart(): void
    {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-020',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(EditCart::class, [
                'cart' => $cart,
            ])
            ->set('code', 'go-021')
            ->set('operational_status', 'maintenance')
            ->set('notes', 'Needs service')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.carts.index'));

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'code' => 'GO-021',
            'operational_status' => 'maintenance',
            'notes' => 'Needs service',
        ]);
    }

    public function test_completed_assignment_history_prevents_cart_type_change(): void
    {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-022',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        [$item, $assignment] = $this->assignCartToBooking(
            $cart,
            'completed'
        );

        $itemSnapshot = $item->getAttributes();
        $assignmentSnapshot = $assignment->getAttributes();

        Livewire::actingAs($user)
            ->test(EditCart::class, [
                'cart' => $cart,
            ])
            ->set('cart_type', '6_seater')
            ->call('save')
            ->assertHasErrors(['cart_type'])
            ->assertSee(
                'Cart type cannot be changed after the cart has assignment history.'
            );

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'cart_type' => '4_seater',
        ]);

        $this->assertEquals(
            $itemSnapshot,
            $item->fresh()->getAttributes()
        );

        $this->assertEquals(
            $assignmentSnapshot,
            $assignment->fresh()->getAttributes()
        );
    }

    public function test_unused_cart_type_can_be_changed(): void
    {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-023',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(EditCart::class, [
                'cart' => $cart,
            ])
            ->set('cart_type', '6_seater')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.carts.index'));

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'cart_type' => '6_seater',
        ]);
    }

    public function test_assigned_cart_operational_status_can_be_changed_when_type_is_unchanged(): void
    {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-024',
            'cart_type' => '4_seater',
            'operational_status' => 'active',
        ]);

        [$item, $assignment] = $this->assignCartToBooking(
            $cart,
            'completed'
        );

        Livewire::actingAs($user)
            ->test(EditCart::class, [
                'cart' => $cart,
            ])
            ->set('operational_status', 'maintenance')
            ->set('notes', 'Scheduled service')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.carts.index'));

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'cart_type' => '4_seater',
            'operational_status' => 'maintenance',
            'notes' => 'Scheduled service',
        ]);

        $this->assertDatabaseHas('booking_items', [
            'id' => $item->id,
            'cart_type' => '4_seater',
        ]);

        $this->assertDatabaseHas('booking_cart_assignments', [
            'id' => $assignment->id,
            'booking_item_id' => $item->id,
            'cart_id' => $cart->id,
        ]);
    }

    public function test_authenticated_user_can_delete_unassigned_cart(): void
    {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-030',
            'cart_type' => '4_seater',
            'operational_status' => 'inactive',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('delete', $cart->id);

        $this->assertDatabaseMissing('carts', [
            'id' => $cart->id,
        ]);
    }

    public function test_cart_with_booking_history_cannot_be_deleted(): void
    {
        $user = User::factory()->create();

        $cart = Cart::create([
            'code' => 'GO-040',
            'cart_type' => '6_seater',
            'operational_status' => 'active',
        ]);

        $booking = Booking::create([
            'full_name' => 'Fleet Test Customer',
            'email' => 'fleet@example.com',
            'phone' => '501-555-0400',
            'pickup_location' => 'hotel',
            'pickup_at' => '2026-09-10 10:00:00',
            'return_at' => '2026-09-12 10:00:00',
            'total_price' => 0.00,
            'status' => 'confirmed',
        ]);

        $item = $booking->items()->create([
            'cart_type' => '6_seater',
            'quantity' => 1,
        ]);

        $item->assignments()->create([
            'cart_id' => $cart->id,
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('delete', $cart->id);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'code' => 'GO-040',
        ]);
    }

    private function assignCartToBooking(
        Cart $cart,
        string $status
    ): array {
        $booking = Booking::create([
            'full_name' => 'Historical Fleet Customer',
            'email' => 'history@example.com',
            'phone' => '501-555-0420',
            'pickup_location' => 'hotel',
            'pickup_at' => '2026-09-10 10:00:00',
            'return_at' => '2026-09-12 10:00:00',
            'total_price' => 0.00,
            'status' => $status,
        ]);

        $item = $booking->items()->create([
            'cart_type' => $cart->cart_type,
            'quantity' => 1,
        ]);

        $assignment = $item->assignments()->create([
            'cart_id' => $cart->id,
        ]);

        return [$item, $assignment];
    }
}
