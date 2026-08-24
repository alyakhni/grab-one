<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContactCustomerLinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_contact_links_existing_customer(): void
    {
        $customer = Customer::create([
            'name' => 'Known Public Guest',
            'email' => 'known.public@example.com',
            'email_normalized' =>
                'known.public@example.com',
            'phone' => '610-9101',
            'phone_normalized' =>
                '+5016109101',
        ]);

        $response = $this
            ->from('/')
            ->post(
                route('contact.send'),
                [
                    'name' =>
                        'Known Public Guest',
                    'email' =>
                        'KNOWN.PUBLIC@example.com',
                    'phone' =>
                        '610-9101',
                    'message' =>
                        'Public known customer inquiry.',
                ]
            );

        $response->assertRedirect('/');

        $contact = Contact::query()
            ->where(
                'message',
                'Public known customer inquiry.'
            )
            ->firstOrFail();

        $this->assertSame(
            $customer->id,
            $contact->customer_id
        );

        $this->assertFalse(
            $contact->is_read
        );

        $this->assertSame(
            'pending',
            $contact->status
        );

        $this->assertSame(
            1,
            Customer::count()
        );
    }

    public function test_public_contact_does_not_create_unknown_customer(): void
    {
        $response = $this
            ->from('/')
            ->post(
                route('contact.send'),
                [
                    'name' =>
                        'Unknown Public Guest',
                    'email' =>
                        'unknown.public@example.com',
                    'phone' =>
                        '610-9199',
                    'message' =>
                        'Public unknown customer inquiry.',
                ]
            );

        $response->assertRedirect('/');

        $contact = Contact::query()
            ->where(
                'message',
                'Public unknown customer inquiry.'
            )
            ->firstOrFail();

        $this->assertNull(
            $contact->customer_id
        );

        $this->assertSame(
            0,
            Customer::count()
        );
    }

    public function test_public_contact_does_not_link_conflicting_identity(): void
    {
        $emailCustomer = Customer::create([
            'name' => 'Email Identity',
            'email' => 'email.identity@example.com',
            'email_normalized' =>
                'email.identity@example.com',
            'phone' => '610-9201',
            'phone_normalized' =>
                '+5016109201',
        ]);

        $phoneCustomer = Customer::create([
            'name' => 'Phone Identity',
            'email' => 'phone.identity@example.com',
            'email_normalized' =>
                'phone.identity@example.com',
            'phone' => '610-9202',
            'phone_normalized' =>
                '+5016109202',
        ]);

        $response = $this
            ->from('/')
            ->post(
                route('contact.send'),
                [
                    'name' =>
                        'Conflict Public Guest',
                    'email' =>
                        $emailCustomer->email,
                    'phone' =>
                        $phoneCustomer->phone,
                    'message' =>
                        'Public conflicting identity inquiry.',
                ]
            );

        $response->assertRedirect('/');

        $contact = Contact::query()
            ->where(
                'message',
                'Public conflicting identity inquiry.'
            )
            ->firstOrFail();

        $this->assertNull(
            $contact->customer_id
        );

        $this->assertSame(
            2,
            Customer::count()
        );
    }
}