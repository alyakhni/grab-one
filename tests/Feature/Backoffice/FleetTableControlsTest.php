<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Carts\Index;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FleetTableControlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_fleet_filters_can_be_applied_and_cleared(): void
    {
        $user = User::factory()->create();

        $matching = $this->createCart(
            'TEST-4-ACTIVE',
            '4_seater',
            'active'
        );

        $this->createCart(
            'TEST-4-MAINT',
            '4_seater',
            'maintenance'
        );

        $this->createCart(
            'TEST-6-ACTIVE',
            '6_seater',
            'active'
        );

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openFilters')
            ->set('filterCartType', '4_seater')
            ->set('filterStatus', 'active')
            ->call('applyFilters')
            ->assertSet('cartType', '4_seater')
            ->assertSet('status', 'active')
            ->assertSee($matching->code)
            ->assertDontSee('TEST-4-MAINT')
            ->assertDontSee('TEST-6-ACTIVE')
            ->call('clearFilters')
            ->assertSet('cartType', '')
            ->assertSet('status', '')
            ->assertSee('TEST-4-MAINT')
            ->assertSee('TEST-6-ACTIVE');
    }

    public function test_fleet_sort_toggles_between_ascending_and_descending(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSet('sortField', 'code')
            ->assertSet('sortDirection', 'asc')
            ->call('sortBy', 'code')
            ->assertSet('sortDirection', 'desc')
            ->call('sortBy', 'code')
            ->assertSet('sortDirection', 'asc')
            ->call('sortBy', 'notes')
            ->assertSet('sortField', 'notes')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_unsortable_fleet_field_is_ignored(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('sortBy', 'id')
            ->assertSet('sortField', 'code')
            ->assertSet('sortDirection', 'asc');
    }

    private function createCart(
        string $code,
        string $cartType,
        string $status
    ): Cart {
        return Cart::create([
            'code' => $code,
            'cart_type' => $cartType,
            'operational_status' => $status,
            'notes' => "Notes for {$code}",
        ]);
    }
}
