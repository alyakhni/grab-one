<?php

namespace Tests\Feature\Backoffice;

use App\Livewire\Backoffice\Contacts\Index;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ContactTableControlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_filter_modal_can_be_opened(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openFilters')
            ->assertSet('showFilters', true)
            ->assertSee('Contact Filters');
    }

    public function test_contact_filters_can_be_combined_and_cleared(): void
    {
        $user = User::factory()->create();

        $oldPending = $this->createContact(
            'Old Pending Contact',
            'pending',
            false,
            '2026-08-20 10:00:00'
        );

        $target = $this->createContact(
            'Target Resolved Contact',
            'resolved',
            true,
            '2026-08-24 15:30:00'
        );

        $unreadResolved = $this->createContact(
            'Unread Resolved Contact',
            'resolved',
            false,
            '2026-08-24 18:00:00'
        );

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openFilters')
            ->set('filterStatus', 'resolved')
            ->set('filterReadStatus', 'read')
            ->set('filterDateFrom', '2026-08-24')
            ->set('filterDateTo', '2026-08-24')
            ->call('applyFilters')
            ->assertHasNoErrors()
            ->assertSet('status', 'resolved')
            ->assertSet('readStatus', 'read')
            ->assertSet('dateFrom', '2026-08-24')
            ->assertSet('dateTo', '2026-08-24')
            ->assertSet('showFilters', false)
            ->assertSee($target->name)
            ->assertDontSee($oldPending->name)
            ->assertDontSee($unreadResolved->name)
            ->call('clearFilters')
            ->assertSet('status', '')
            ->assertSet('readStatus', '')
            ->assertSet('dateFrom', '')
            ->assertSet('dateTo', '')
            ->assertSee($target->name)
            ->assertSee($oldPending->name)
            ->assertSee($unreadResolved->name);
    }

    public function test_contact_date_filter_rejects_reversed_range(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openFilters')
            ->set('filterDateFrom', '2026-08-25')
            ->set('filterDateTo', '2026-08-24')
            ->call('applyFilters')
            ->assertHasErrors([
                'filterDateTo',
            ]);
    }

    public function test_contact_received_sort_toggles_between_ascending_and_descending(): void
    {
        $user = User::factory()->create();

        $earlier = $this->createContact(
            'Earlier Contact',
            'pending',
            false,
            '2026-08-20 09:00:00'
        );

        $later = $this->createContact(
            'Later Contact',
            'pending',
            false,
            '2026-08-24 09:00:00'
        );

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSet('sortField', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSeeInOrder([
                $later->name,
                $earlier->name,
            ])
            ->call('sortBy', 'created_at')
            ->assertSet('sortDirection', 'asc')
            ->assertSeeInOrder([
                $earlier->name,
                $later->name,
            ])
            ->call('sortBy', 'created_at')
            ->assertSet('sortDirection', 'desc')
            ->assertSeeInOrder([
                $later->name,
                $earlier->name,
            ]);
    }

    public function test_unsortable_contact_field_is_ignored(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('sortBy', 'not_allowed')
            ->assertSet('sortField', 'created_at')
            ->assertSet('sortDirection', 'desc');
    }

    private function createContact(
        string $name,
        string $status,
        bool $isRead,
        string $createdAt
    ): Contact {
        $contact = Contact::create([
            'name' => $name,
            'email' => strtolower(
                str_replace(
                    ' ',
                    '.',
                    $name
                )
            ).'@example.com',
            'phone' => '+5016103000',
            'message' => 'Table controls test message.',
            'status' => $status,
            'is_read' => $isRead,
        ]);

        DB::table('contacts')
            ->where(
                'id',
                $contact->id
            )
            ->update([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

        return $contact->fresh();
    }
}