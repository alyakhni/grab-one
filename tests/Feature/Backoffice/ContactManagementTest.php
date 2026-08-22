<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Contacts\CreateContact;
use App\Livewire\Backoffice\Contacts\EditContact;
use App\Livewire\Backoffice\Contacts\Index;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_contacts(): void
    {
        $this->get(route('backoffice.contacts.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_contacts(): void
    {
        $user = User::factory()->create();

        $contact = $this->createContact([
            'name' => 'Maria Johnson',
        ]);

        $this->actingAs($user)
            ->get(route('backoffice.contacts.index'))
            ->assertOk()
            ->assertSee($contact->name);
    }

    public function test_contacts_can_be_searched(): void
    {
        $user = User::factory()->create();

        $this->createContact([
            'name' => 'Alice Customer',
        ]);

        $this->createContact([
            'name' => 'Bob Customer',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('search', 'Alice')
            ->assertSee('Alice Customer')
            ->assertDontSee('Bob Customer');
    }

    public function test_contacts_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();

        $this->createContact([
            'name' => 'Pending Customer',
            'status' => 'pending',
        ]);

        $this->createContact([
            'name' => 'Resolved Customer',
            'status' => 'resolved',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('status', 'resolved')
            ->assertSee('Resolved Customer')
            ->assertDontSee('Pending Customer');
    }

    public function test_contacts_can_be_filtered_by_read_status(): void
    {
        $user = User::factory()->create();

        $this->createContact([
            'name' => 'Unread Customer',
            'is_read' => false,
        ]);

        $this->createContact([
            'name' => 'Read Customer',
            'is_read' => true,
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('readStatus', 'unread')
            ->assertSee('Unread Customer')
            ->assertDontSee('Read Customer');
    }

    public function test_authenticated_user_can_create_contact(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateContact::class)
            ->set('name', 'New Customer')
            ->set('email', 'new@example.com')
            ->set('phone', '501-555-0100')
            ->set('message', 'I would like more information.')
            ->set('status', 'pending')
            ->set('is_read', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('backoffice.contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'name' => 'New Customer',
            'email' => 'new@example.com',
            'status' => 'pending',
            'is_read' => 0,
        ]);
    }

    public function test_authenticated_user_can_edit_contact(): void
    {
        $user = User::factory()->create();
        $contact = $this->createContact();

        Livewire::actingAs($user)
            ->test(EditContact::class, ['contact' => $contact])
            ->set('name', 'Updated Customer')
            ->set('status', 'resolved')
            ->set('is_read', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('backoffice.contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'Updated Customer',
            'status' => 'resolved',
            'is_read' => 1,
        ]);
    }

    public function test_authenticated_user_can_toggle_read_status(): void
    {
        $user = User::factory()->create();

        $contact = $this->createContact([
            'is_read' => false,
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('toggleRead', $contact->id);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'is_read' => 1,
        ]);
    }

    public function test_authenticated_user_can_delete_contact(): void
    {
        $user = User::factory()->create();
        $contact = $this->createContact();

        Livewire::actingAs($user)
            ->test(EditContact::class, ['contact' => $contact])
            ->call('delete')
            ->assertRedirect(route('backoffice.contacts.index'));

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_authenticated_user_can_bulk_delete_contacts(): void
    {
        $user = User::factory()->create();

        $first = $this->createContact([
            'name' => 'First Customer',
        ]);

        $second = $this->createContact([
            'name' => 'Second Customer',
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('selected', [
                (string) $first->id,
                (string) $second->id,
            ])
            ->call('deleteSelected');

        $this->assertDatabaseMissing('contacts', ['id' => $first->id]);
        $this->assertDatabaseMissing('contacts', ['id' => $second->id]);
    }

    private function createContact(array $overrides = []): Contact
    {
        return Contact::create(array_merge([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '501-555-0000',
            'message' => 'Test contact message.',
            'status' => 'pending',
            'is_read' => false,
        ], $overrides));
    }
}