<?php

namespace Tests\Feature;

use App\Livewire\Backoffice\Bookings\Index as BookingsIndex;
use App\Livewire\Backoffice\Contacts\Index as ContactsIndex;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerIdentityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IdentityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_retro_links_safe_historical_contact_by_email(): void
    {
        $contact = Contact::create([
            'name' =>
                'Historical Guest',
            'email' =>
                'historical@example.com',
            'phone' =>
                '610-1101',
            'message' =>
                'Contact before booking.',
            'status' =>
                'pending',
            'is_read' =>
                false,
        ]);

        $this->assertNull(
            $contact->customer_id
        );

        $customer = app(
            CustomerIdentityService::class
        )->resolveForBooking(
            'Historical Guest',
            'HISTORICAL@example.com',
            '610-1101'
        );

        $this->assertNotNull(
            $customer
        );

        $this->assertSame(
            $customer->id,
            $contact
                ->fresh()
                ->customer_id
        );
    }

    public function test_booking_retro_links_safe_historical_contact_by_phone(): void
    {
        $contact = Contact::create([
            'name' =>
                'Phone History Guest',
            'email' =>
                'old-contact@example.com',
            'phone' =>
                '610-2202',
            'message' =>
                'Phone matched historical inquiry.',
            'status' =>
                'pending',
            'is_read' =>
                false,
        ]);

        $customer = app(
            CustomerIdentityService::class
        )->resolveForBooking(
            'Phone History Guest',
            'booking-address@example.com',
            '+501 610-2202'
        );

        $this->assertNotNull(
            $customer
        );

        $this->assertSame(
            $customer->id,
            $contact
                ->fresh()
                ->customer_id
        );
    }

    public function test_conflicting_historical_contact_is_never_retro_linked(): void
    {
        $emailCustomer =
            $this->createCustomer(
                'Email Customer',
                'email-owner@example.com',
                '610-3301'
            );

        $phoneCustomer =
            $this->createCustomer(
                'Phone Customer',
                'phone-owner@example.com',
                '610-3302'
            );

        $contact = Contact::create([
            'name' =>
                'Conflict Contact',
            'email' =>
                $emailCustomer->email,
            'phone' =>
                $phoneCustomer->phone,
            'message' =>
                'Conflicting historical inquiry.',
            'status' =>
                'pending',
            'is_read' =>
                false,
        ]);

        $resolved = app(
            CustomerIdentityService::class
        )->resolveForBooking(
            'Email Customer',
            $emailCustomer->email,
            $emailCustomer->phone
        );

        $this->assertSame(
            $emailCustomer->id,
            $resolved?->id
        );

        $this->assertNull(
            $contact
                ->fresh()
                ->customer_id
        );

        $this->assertSame(
            2,
            Customer::count()
        );
    }

    public function test_booking_index_surfaces_identity_review_for_conflict(): void
    {
        $user =
            User::factory()->create();

        $emailCustomer =
            $this->createCustomer(
                'Booking Email Owner',
                'booking-email@example.com',
                '610-4401'
            );

        $phoneCustomer =
            $this->createCustomer(
                'Booking Phone Owner',
                'booking-phone@example.com',
                '610-4402'
            );

        $this->createBooking(
            'Conflict Booking',
            $emailCustomer->email,
            $phoneCustomer->phone
        );

        $this->createBooking(
            'Ordinary Unlinked Booking',
            'ordinary@example.com',
            '610-4499'
        );

        $component =
            Livewire::actingAs($user)
                ->test(
                    BookingsIndex::class
                );

        $component
            ->assertSee(
                'Conflict Booking'
            )
            ->assertSee(
                'Ordinary Unlinked Booking'
            )
            ->assertSee(
                'Identity Review'
            );

        $this->assertSame(
            1,
            substr_count(
                $component->html(),
                'Identity Review'
            )
        );
    }

    public function test_contact_index_surfaces_identity_review_for_conflict_only(): void
    {
        $user =
            User::factory()->create();

        $emailCustomer =
            $this->createCustomer(
                'Contact Email Owner',
                'contact-email@example.com',
                '610-5501'
            );

        $phoneCustomer =
            $this->createCustomer(
                'Contact Phone Owner',
                'contact-phone@example.com',
                '610-5502'
            );

        Contact::create([
            'name' =>
                'Conflict Contact Row',
            'email' =>
                $emailCustomer->email,
            'phone' =>
                $phoneCustomer->phone,
            'message' =>
                'Conflict row.',
            'status' =>
                'pending',
            'is_read' =>
                false,
        ]);

        Contact::create([
            'name' =>
                'Ordinary Inquiry',
            'email' =>
                'ordinary-inquiry@example.com',
            'phone' =>
                '610-5599',
            'message' =>
                'Ordinary unknown inquiry.',
            'status' =>
                'pending',
            'is_read' =>
                false,
        ]);

        $component =
            Livewire::actingAs($user)
                ->test(
                    ContactsIndex::class
                );

        $component
            ->assertSee(
                'Conflict Contact Row'
            )
            ->assertSee(
                'Ordinary Inquiry'
            )
            ->assertSee(
                'Identity Review'
            );

        $this->assertSame(
            1,
            substr_count(
                $component->html(),
                'Identity Review'
            )
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
        string $name,
        string $email,
        string $phone
    ): Booking {
        return Booking::create([
            'full_name' =>
                $name,

            'email' =>
                $email,

            'phone' =>
                $phone,

            'hotel_name' =>
                'Identity Test Hotel',

            'pickup_location' =>
                'hotel',

            'pickup_at' =>
                '2026-08-28 09:00:00',

            'return_at' =>
                '2026-08-28 17:00:00',

            'total_price' =>
                0,

            'status' =>
                'pending',
        ]);
    }
}