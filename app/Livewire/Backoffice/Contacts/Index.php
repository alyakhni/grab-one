<?php

namespace App\Livewire\Backoffice\Contacts;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::backoffice')]
#[Title('Contacts | Grab One')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $readStatus = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public array $selected = [];

    protected array $sortableFields = [
        'name',
        'email',
        'status',
        'is_read',
        'created_at',
        'updated_at',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedReadStatus(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, $this->sortableFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc'
                ? 'desc'
                : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function toggleRead(int $contactId): void
    {
        $contact = Contact::query()->findOrFail($contactId);

        $contact->update([
            'is_read' => ! $contact->is_read,
        ]);
    }

    public function delete(int $contactId): void
    {
        Contact::query()->findOrFail($contactId)->delete();

        $this->selected = array_values(
            array_filter(
                $this->selected,
                fn ($id): bool => (int) $id !== $contactId
            )
        );

        session()->flash('success', 'Contact message deleted successfully.');
    }

    public function deleteSelected(): void
    {
        $ids = collect($this->selected)
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($ids === []) {
            return;
        }

        Contact::query()
            ->whereIn('id', $ids)
            ->delete();

        $this->selected = [];

        session()->flash('success', 'Selected contact messages deleted successfully.');
    }

    protected function contactsQuery(): Builder
    {
        $search = trim($this->search);

        return Contact::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->when(
                $this->status !== '',
                fn (Builder $query): Builder => $query->where('status', $this->status)
            )
            ->when(
                $this->readStatus === 'read',
                fn (Builder $query): Builder => $query->where('is_read', true)
            )
            ->when(
                $this->readStatus === 'unread',
                fn (Builder $query): Builder => $query->where('is_read', false)
            )
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.backoffice.contacts.index', [
            'contacts' => $this->contactsQuery()->paginate(10),
            'statuses' => Contact::STATUSES,
        ]);
    }
}