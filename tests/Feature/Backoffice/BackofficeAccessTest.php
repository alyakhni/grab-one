<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BackofficeAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_backoffice_login_page(): void
    {
        $this->get(route('login'))
            ->assertOk();
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_user_can_log_in_to_backoffice(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        Livewire::test(Login::class)
            ->set('email', 'admin@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_log_out_of_backoffice(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}