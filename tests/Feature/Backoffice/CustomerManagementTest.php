<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Customers\Index as CustomersIndex;
use App\Livewire\Backoffice\Customers\Show as CustomerShow;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_customers(): void
    {
        $customer = $this->createCustomer(
            'Protected Customer',
            'protected@example.com',
            '610-1001'
        );

        $this->get(
            route('admin.customers.index')
        )->assertRedirect(
            route('login')
        );

        $this->get(
            route(
                'admin.customers.show',
                $customer
            )
        )->assertRedirect(
            route('login')
        );
    }

    public function test_authenticated_user_can_view_customers_and_navigation(): void
    {
        $user = User::factory()->create();

        $customer = $this->createCustomer(
            'Visible Customer',
            'visible@example.com',
            '610-1002'
        );

        $this->actingAs($user)
            ->get(
                route('admin.customers.index')
            )
            ->assertOk()
            ->assertSee(
                'Customers'
            )
            ->assertSee(
                $customer->name
            );

        $this->actingAs($user)
            ->get(
                route('admin.dashboard')
            )
            ->assertOk()
            ->assertSee(
                route(
                    'admin.customers.index'
                ),
                false
            );
    }

    public function test_customers_can_be_searched(): void
    {
        $user = User::factory()->create();

        $target = $this->createCustomer(
            'Search Target',
            'target@example.com',
            '610-2001'
        );

        $other = $this->createCustomer(
            'Other Customer',
            'other@example.com',
            '610-2002'
        );

        Livewire::actingAs($user)
            ->test(
                CustomersIndex::class
            )
            ->set(
                'search',
                'target@example.com'
            )
            ->assertSee(
                $target->name
            )
            ->assertDontSee(
                $other->name
            );
    }

    public function test_customer_sort_toggles_and_ignores_unknown_fields(): void
    {
        $user = User::factory()->create();

        $alpha = $this->createCustomer(
            'Alpha Customer',
            'alpha@example.com',
            '610-3001'
        );

        $zulu = $this->createCustomer(
            'Zulu Customer',
            'zulu@example.com',
            '610-3002'
        );

        Livewire::actingAs($user)
            ->test(
                CustomersIndex::class
            )
            ->call(
                'sortBy',
                'name'
            )
            ->assertSet(
                'sortField',
                'name'
            )
            ->assertSet(
                'sortDirection',
                'asc'
            )
            ->assertSeeInOrder([
                $alpha->name,
                $zulu->name,
            ])
            ->call(
                'sortBy',
                'name'
            )
            ->assertSet(
                'sortDirection',
                'desc'
            )
            ->assertSeeInOrder([
                $zulu->name,
                $alpha->name,
            ])
            ->call(
                'sortBy',
                'not_allowed'
            )
            ->assertSet(
                'sortField',
                'name'
            )
            ->assertSet(
                'sortDirection',
                'desc'
            );
    }

    public function test_customer_index_shows_booking_and_contact_aggregates(): void
    {
        $user = User::factory()->create();

        $customer = $this->createCustomer(
            'Aggregate Customer',
            'aggregate@example.com',
            '610-4001'
        );

        $this->createBooking(
            $customer,
            '2026-08-25 09:00:00',
            '2026-08-25 17:00:00',
            150.00
        );

        $this->createBooking(
            $customer,
            '2026-08-27 09:00:00',
            '2026-08-27 17:00:00',
            200.00
        );

        Contact::create([
            'customer_id' =>
                $customer->id,
            'name' =>
                $customer->name,
            'email' =>
                $customer->email,
            'phone' =>
                $customer->phone,
            'message' =>
                'Aggregate contact one.',
            'status' =>
                'pending',
            'is_read' =>
                false,
        ]);

        Contact::create([
            'customer_id' =>
                $customer->id,
            'name' =>
                $customer->name,
            'email' =>
                $customer->email,
            'phone' =>
                $customer->phone,
            'message' =>
                'Aggregate contact two.',
            'status' =>
                'resolved',
            'is_read' =>
                true,
        ]);

        Livewire::actingAs($user)
            ->test(
                CustomersIndex::class
            )
            ->assertSee(
                $customer->name
            )
            ->assertSee(
                '$350.00'
            )
            ->assertSee(
                'Aug 27, 2026'
            );
    }

    public function test_customer_history_shows_only_linked_operational_activity(): void
    {
        $user = User::factory()->create();

        $customer = $this->createCustomer(
            'History Customer',
            'history@example.com',
            '610-5001'
        );

        $otherCustomer = $this->createCustomer(
            'Unrelated Customer',
            'unrelated@example.com',
            '610-5002'
        );

        $booking = $this->createBooking(
            $customer,
            '2026-08-28 09:00:00',
            '2026-08-28 17:00:00',
            175.00,
            'confirmed'
        );

        $item = $booking
            ->items()
            ->create([
                'cart_type' =>
                    '4_seater',
                'quantity' =>
                    1,
            ]);

        $cart = Cart::create([
            'code' =>
                'CUSTOMER-HISTORY-01',
            'cart_type' =>
                '4_seater',
            'operational_status' =>
                'active',
            'notes' =>
                'Customer history test cart.',
        ]);

        $item
            ->assignments()
            ->create([
                'cart_id' =>
                    $cart->id,
            ]);

        Contact::create([
            'customer_id' =>
                $customer->id,
            'name' =>
                $customer->name,
            'email' =>
                $customer->email,
            'phone' =>
                $customer->phone,
            'message' =>
                'Linked customer history message.',
            'status' =>
                'working_on_it',
            'is_read' =>
                false,
        ]);

        $this->createBooking(
            $otherCustomer,
            '2026-08-29 09:00:00',
            '2026-08-29 17:00:00',
            999.00
        );

        Contact::create([
            'customer_id' =>
                $otherCustomer->id,
            'name' =>
                $otherCustomer->name,
            'email' =>
                $otherCustomer->email,
            'phone' =>
                $otherCustomer->phone,
            'message' =>
                'Unrelated customer message.',
            'status' =>
                'pending',
            'is_read' =>
                false,
        ]);

        Livewire::actingAs($user)
            ->test(
                CustomerShow::class,
                [
                    'customer' =>
                        $customer,
                ]
            )
            ->assertSee(
                $customer->name
            )
            ->assertSee(
                'Booking #'.$booking->id
            )
            ->assertSee(
                'CUSTOMER-HISTORY-01'
            )
            ->assertSee(
                'Linked customer history message.'
            )
            ->assertSee(
                '$175.00'
            )
            ->assertDontSee(
                'Unrelated Customer'
            )
            ->assertDontSee(
                'Unrelated customer message.'
            )
            ->assertDontSee(
                '$999.00'
            );
    }

    private function createCustomer(
        string $name,
        string $email,
        string $phone
    ): Customer {
        return Customer::create([
            'name' =>
                $name,
            'email' =>
                $email,
            'email_normalized' =>
                strtolower($email),
            'phone' =>
                $phone,
            'phone_normalized' =>
                '+501'.preg_replace(
                    '/\D+/',
                    '',
                    $phone
                ),
        ]);
    }

    private function createBooking(
        Customer $customer,
        string $pickupAt,
        string $returnAt,
        float $price,
        string $status = 'completed'
    ): Booking {
        return Booking::create([
            'customer_id' =>
                $customer->id,
            'full_name' =>
                $customer->name,
            'email' =>
                $customer->email,
            'phone' =>
                $customer->phone,
            'hotel_name' =>
                'Customer Test Hotel',
            'pickup_location' =>
                'hotel',
            'pickup_at' =>
                $pickupAt,
            'return_at' =>
                $returnAt,
            'total_price' =>
                $price,
            'status' =>
                $status,
        ]);
    }
}