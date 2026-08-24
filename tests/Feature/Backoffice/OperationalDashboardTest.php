<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Dashboard;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\Contact;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OperationalDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_reports_operational_metrics_in_belize_time(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-24 10:00:00',
                'America/Belize'
            )
        );

        $user = User::factory()->create();

        $availableCart = $this->createCart(
            'DASH-AVAILABLE'
        );

        $currentCart = $this->createCart(
            'DASH-CURRENT'
        );

        $bufferCart = $this->createCart(
            'DASH-BUFFER'
        );

        $this->createCart(
            'DASH-MAINTENANCE',
            'maintenance'
        );

        $currentBooking = $this->createBooking(
            'Current Rental Guest',
            'confirmed',
            '2026-08-24 09:00:00',
            '2026-08-24 12:00:00'
        );

        $this->assign(
            $currentBooking,
            $currentCart
        );

        $bufferBooking = $this->createBooking(
            'Buffer Guest',
            'confirmed',
            '2026-08-24 07:00:00',
            '2026-08-24 09:30:00'
        );

        $this->assign(
            $bufferBooking,
            $bufferCart
        );

        $this->createBooking(
            'Pending Guest',
            'pending',
            '2026-08-24 14:00:00',
            '2026-08-24 18:00:00'
        );

        Contact::create([
            'name' => 'Unread Guest',
            'email' => 'unread@example.com',
            'phone' => '+5016101111',
            'message' => 'Unread dashboard message.',
            'status' => 'pending',
            'is_read' => false,
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertViewHas(
                'pendingRequests',
                1
            )
            ->assertViewHas(
                'currentRentals',
                1
            )
            ->assertViewHas(
                'confirmedPickupsToday',
                2
            )
            ->assertViewHas(
                'returnsToday',
                2
            )
            ->assertViewHas(
                'totalFleet',
                4
            )
            ->assertViewHas(
                'availableNow',
                1
            )
            ->assertViewHas(
                'maintenanceCarts',
                1
            )
            ->assertViewHas(
                'unreadContacts',
                1
            )
            ->assertSee(
                $availableCart->code
            )
            ->assertSee(
                'Belize Time'
            );
    }

    public function test_schedule_uses_date_overlap_and_can_filter_by_cart(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-24 10:00:00',
                'America/Belize'
            )
        );

        $user = User::factory()->create();

        $cartA = $this->createCart(
            'DASH-A'
        );

        $cartB = $this->createCart(
            'DASH-B'
        );

        $overlapBooking =
            $this->createBooking(
                'Overlap Guest',
                'confirmed',
                '2026-08-24 23:00:00',
                '2026-08-25 02:00:00'
            );

        $this->assign(
            $overlapBooking,
            $cartA
        );

        $laterBooking =
            $this->createBooking(
                'Later Guest',
                'confirmed',
                '2026-08-26 09:00:00',
                '2026-08-26 12:00:00'
            );

        $this->assign(
            $laterBooking,
            $cartB
        );

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call(
                'setScheduleMode',
                'specific'
            )
            ->set(
                'specificDate',
                '2026-08-25'
            )
            ->assertSee(
                'Overlap Guest'
            )
            ->assertDontSee(
                'Later Guest'
            )
            ->set(
                'cartId',
                (string) $cartB->id
            )
            ->assertDontSee(
                'Overlap Guest'
            )
            ->call(
                'setScheduleMode',
                'range'
            )
            ->set(
                'dateFrom',
                '2026-08-25'
            )
            ->set(
                'dateTo',
                '2026-08-26'
            )
            ->assertHasNoErrors(
                'dateTo'
            )
            ->assertSee(
                'Later Guest'
            )
            ->assertDontSee(
                'Overlap Guest'
            );
    }

    public function test_schedule_rejects_reversed_date_range(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-24 10:00:00',
                'America/Belize'
            )
        );

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call(
                'setScheduleMode',
                'range'
            )
            ->set(
                'dateFrom',
                '2026-08-26'
            )
            ->set(
                'dateTo',
                '2026-08-25'
            )
            ->assertHasErrors(
                'dateTo'
            );
    }

    public function test_schedule_mode_rejects_unknown_value(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call(
                'setScheduleMode',
                'not-valid'
            )
            ->assertSet(
                'scheduleMode',
                'today'
            );
    }

    private function createCart(
        string $code,
        string $status = 'active'
    ): Cart {
        return Cart::create([
            'code' => $code,
            'cart_type' => '4_seater',
            'operational_status' => $status,
            'notes' => 'Operational dashboard test cart.',
        ]);
    }

    private function createBooking(
        string $name,
        string $status,
        string $pickupAt,
        string $returnAt
    ): Booking {
        return Booking::create([
            'full_name' => $name,
            'email' => strtolower(
                str_replace(
                    ' ',
                    '.',
                    $name
                )
            ).'@example.com',
            'phone' => '+5016102222',
            'hotel_name' => 'Dashboard Hotel',
            'pickup_location' => 'hotel',
            'pickup_at' => $pickupAt,
            'return_at' => $returnAt,
            'total_price' => 100.00,
            'status' => $status,
        ]);
    }

    private function assign(
        Booking $booking,
        Cart $cart
    ): void {
        $item = $booking
            ->items()
            ->create([
                'cart_type' =>
                    $cart->cart_type,
                'quantity' => 1,
            ]);

        $item
            ->assignments()
            ->create([
                'cart_id' =>
                    $cart->id,
            ]);
    }
}