<?php

namespace Tests\Feature;

use App\Livewire\Backoffice\Bookings\CreateBooking;
use App\Livewire\Backoffice\Contacts\CreateContact;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerIdentityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerIdentityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_and_belize_phone_are_normalized(): void
    {
        $service = app(
            CustomerIdentityService::class
        );

        $this->assertSame(
            'guest@example.com',
            $service->normalizeEmail(
                '  Guest@Example.COM '
            )
        );

        $this->assertSame(
            '+5016101234',
            $service->normalizePhone(
                '610-1234'
            )
        );

        $this->assertSame(
            '+5016101234',
            $service->normalizePhone(
                '+501 610-1234'
            )
        );
    }

    public function test_booking_identity_creates_customer_when_no_match_exists(): void
    {
        $customer = app(
            CustomerIdentityService::class
        )->resolveForBooking(
            'New Guest',
            'New.Guest@Example.com',
            '610-1111'
        );

        $this->assertInstanceOf(
            Customer::class,
            $customer
        );

        $this->assertDatabaseHas(
            'customers',
            [
                'id' => $customer->id,
                'email_normalized' =>
                    'new.guest@example.com',
                'phone_normalized' =>
                    '+5016101111',
            ]
        );
    }

    public function test_booking_identity_reuses_customer_by_email(): void
    {
        $existing = Customer::create([
            'name' => 'Existing Guest',
            'email' => 'guest@example.com',
            'email_normalized' =>
                'guest@example.com',
            'phone' => '610-1000',
            'phone_normalized' =>
                '+5016101000',
        ]);

        $resolved = app(
            CustomerIdentityService::class
        )->resolveForBooking(
            'Updated Guest Name',
            ' GUEST@example.com ',
            '610-2000'
        );

        $this->assertSame(
            $existing->id,
            $resolved?->id
        );

        $this->assertSame(
            1,
            Customer::count()
        );

        $this->assertDatabaseHas(
            'customers',
            [
                'id' => $existing->id,
                'name' =>
                    'Updated Guest Name',
                'phone_normalized' =>
                    '+5016102000',
            ]
        );
    }

    public function test_booking_identity_reuses_customer_by_phone_when_email_has_no_match(): void
    {
        $existing = Customer::create([
            'name' => 'Phone Guest',
            'email' => 'old@example.com',
            'email_normalized' =>
                'old@example.com',
            'phone' => '+501 610-3333',
            'phone_normalized' =>
                '+5016103333',
        ]);

        $resolved = app(
            CustomerIdentityService::class
        )->resolveForBooking(
            'Phone Guest',
            'new@example.com',
            '610-3333'
        );

        $this->assertSame(
            $existing->id,
            $resolved?->id
        );

        $this->assertSame(
            1,
            Customer::count()
        );
    }

    public function test_conflicting_email_and_phone_never_auto_merge_customers(): void
    {
        $emailCustomer = Customer::create([
            'name' => 'Email Customer',
            'email' => 'email@example.com',
            'email_normalized' =>
                'email@example.com',
            'phone' => '610-4001',
            'phone_normalized' =>
                '+5016104001',
        ]);

        $phoneCustomer = Customer::create([
            'name' => 'Phone Customer',
            'email' => 'phone@example.com',
            'email_normalized' =>
                'phone@example.com',
            'phone' => '610-4002',
            'phone_normalized' =>
                '+5016104002',
        ]);

        $service = app(
            CustomerIdentityService::class
        );

        $resolved =
            $service->resolveForBooking(
                'Conflict Guest',
                $emailCustomer->email,
                $phoneCustomer->phone
            );

        $this->assertNull(
            $resolved
        );

        $this->assertTrue(
            $service->hasConflict(
                $emailCustomer->email,
                $phoneCustomer->phone
            )
        );

        $this->assertSame(
            2,
            Customer::count()
        );
    }

    public function test_ambiguous_identifier_is_not_auto_linked(): void
    {
        Customer::create([
            'name' => 'Shared One',
            'email' => 'shared@example.com',
            'email_normalized' =>
                'shared@example.com',
            'phone' => '610-5001',
            'phone_normalized' =>
                '+5016105001',
        ]);

        Customer::create([
            'name' => 'Shared Two',
            'email' => 'shared@example.com',
            'email_normalized' =>
                'shared@example.com',
            'phone' => '610-5002',
            'phone_normalized' =>
                '+5016105002',
        ]);

        $service = app(
            CustomerIdentityService::class
        );

        $this->assertNull(
            $service->resolveForBooking(
                'Ambiguous Guest',
                'shared@example.com',
                '610-5999'
            )
        );

        $this->assertTrue(
            $service->hasConflict(
                'shared@example.com',
                '610-5999'
            )
        );

        $this->assertSame(
            2,
            Customer::count()
        );
    }

    public function test_contact_links_existing_customer_but_never_creates_unknown_customer(): void
    {
        $existing = Customer::create([
            'name' => 'Known Guest',
            'email' => 'known@example.com',
            'email_normalized' =>
                'known@example.com',
            'phone' => '610-6001',
            'phone_normalized' =>
                '+5016106001',
        ]);

        $service = app(
            CustomerIdentityService::class
        );

        $known =
            $service->findExistingForContact(
                'KNOWN@example.com',
                '610-6001'
            );

        $unknown =
            $service->findExistingForContact(
                'unknown@example.com',
                '610-6999'
            );

        $this->assertSame(
            $existing->id,
            $known?->id
        );

        $this->assertNull(
            $unknown
        );

        $this->assertSame(
            1,
            Customer::count()
        );
    }

    public function test_admin_booking_creation_creates_and_links_customer(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateBooking::class)
            ->set(
                'full_name',
                'Admin Booking Guest'
            )
            ->set(
                'email',
                'admin.booking@example.com'
            )
            ->set(
                'phone',
                '610-7001'
            )
            ->set(
                'pickup_location',
                'hotel'
            )
            ->set(
                'pickup_at',
                '2026-08-26T09:00'
            )
            ->set(
                'return_at',
                '2026-08-26T17:00'
            )
            ->set(
                'cart_selection',
                '4_seater'
            )
            ->set(
                'cart_quantities.4_seater',
                1
            )
            ->set(
                'total_price',
                '100.00'
            )
            ->set(
                'status',
                'pending'
            )
            ->call('save')
            ->assertHasNoErrors();

        $customer = Customer::query()
            ->where(
                'email_normalized',
                'admin.booking@example.com'
            )
            ->firstOrFail();

        $booking = Booking::query()
            ->where(
                'email',
                'admin.booking@example.com'
            )
            ->firstOrFail();

        $this->assertSame(
            $customer->id,
            $booking->customer_id
        );
    }

    public function test_admin_contact_links_known_customer_and_does_not_create_unknown_customer(): void
    {
        $user = User::factory()->create();

        $customer = Customer::create([
            'name' => 'Contact Customer',
            'email' =>
                'contact.customer@example.com',
            'email_normalized' =>
                'contact.customer@example.com',
            'phone' => '610-8001',
            'phone_normalized' =>
                '+5016108001',
        ]);

        Livewire::actingAs($user)
            ->test(CreateContact::class)
            ->set(
                'name',
                'Contact Customer'
            )
            ->set(
                'email',
                'CONTACT.CUSTOMER@example.com'
            )
            ->set(
                'phone',
                '610-8001'
            )
            ->set(
                'message',
                'Known customer inquiry.'
            )
            ->call('save')
            ->assertHasNoErrors();

        $knownContact = Contact::query()
            ->where(
                'message',
                'Known customer inquiry.'
            )
            ->firstOrFail();

        $this->assertSame(
            $customer->id,
            $knownContact->customer_id
        );

        Livewire::actingAs($user)
            ->test(CreateContact::class)
            ->set(
                'name',
                'Unknown Contact'
            )
            ->set(
                'email',
                'unknown.contact@example.com'
            )
            ->set(
                'phone',
                '610-8999'
            )
            ->set(
                'message',
                'Unknown customer inquiry.'
            )
            ->call('save')
            ->assertHasNoErrors();

        $unknownContact = Contact::query()
            ->where(
                'message',
                'Unknown customer inquiry.'
            )
            ->firstOrFail();

        $this->assertNull(
            $unknownContact->customer_id
        );

        $this->assertSame(
            1,
            Customer::count()
        );
    }
}