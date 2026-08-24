<?php

namespace App\Livewire\Backoffice\Contacts;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
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
    public string $dateFrom = '';
    public string $dateTo = '';

    public string $filterStatus = '';
    public string $filterReadStatus = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    public bool $showFilters = false;

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public array $selected = [];

    protected array $sortableFields = [
        'name',
        'phone',
        'message',
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

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function openFilters(): void
    {
        $this->resetValidation();

        $this->filterStatus = $this->status;
        $this->filterReadStatus = $this->readStatus;
        $this->filterDateFrom = $this->dateFrom;
        $this->filterDateTo = $this->dateTo;
        $this->showFilters = true;
    }

    public function closeFilters(): void
    {
        $this->resetValidation();
        $this->showFilters = false;
    }

    public function applyFilters(): void
    {
        $this->validate([
            'filterStatus' => [
                'nullable',
                Rule::in(
                    array_keys(
                        config(
                            'grabone.contact_statuses',
                            []
                        )
                    )
                ),
            ],
            'filterReadStatus' => [
                'nullable',
                Rule::in([
                    'read',
                    'unread',
                ]),
            ],
            'filterDateFrom' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'filterDateTo' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ]);

        if (
            $this->filterDateFrom !== ''
            && $this->filterDateTo !== ''
            && $this->filterDateTo < $this->filterDateFrom
        ) {
            $this->addError(
                'filterDateTo',
                'The To date must be on or after the From date.'
            );

            return;
        }

        $this->status = $this->filterStatus;
        $this->readStatus = $this->filterReadStatus;
        $this->dateFrom = $this->filterDateFrom;
        $this->dateTo = $this->filterDateTo;
        $this->showFilters = false;

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->resetValidation();

        $this->status = '';
        $this->readStatus = '';
        $this->dateFrom = '';
        $this->dateTo = '';

        $this->filterStatus = '';
        $this->filterReadStatus = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';

        $this->showFilters = false;

        $this->resetPage();
    }

    public function activeFilterCount(): int
    {
        return collect([
            $this->status,
            $this->readStatus,
            $this->dateFrom,
            $this->dateTo,
        ])
            ->filter(
                fn (string $value): bool => $value !== ''
            )
            ->count();
    }

    public function sortBy(string $field): void
    {
        if (! in_array(
            $field,
            $this->sortableFields,
            true
        )) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc'
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

        session()->flash(
            'success',
            'Contact message deleted successfully.'
        );
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

        session()->flash(
            'success',
            'Selected contact messages deleted successfully.'
        );
    }

    protected function contactsQuery(): Builder
    {
        $search = trim($this->search);

        return Contact::query()
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('message', 'like', "%{$search}%")
                                ->orWhere('status', 'like', "%{$search}%");
                        }
                    );
                }
            )
            ->when(
                $this->status !== '',
                fn (Builder $query): Builder =>
                    $query->where('status', $this->status)
            )
            ->when(
                $this->readStatus === 'read',
                fn (Builder $query): Builder =>
                    $query->where('is_read', true)
            )
            ->when(
                $this->readStatus === 'unread',
                fn (Builder $query): Builder =>
                    $query->where('is_read', false)
            )
            ->when(
                $this->dateFrom !== '',
                fn (Builder $query): Builder =>
                    $query->where(
                        'created_at',
                        '>=',
                        $this->dateFrom.' 00:00:00'
                    )
            )
            ->when(
                $this->dateTo !== '',
                fn (Builder $query): Builder =>
                    $query->where(
                        'created_at',
                        '<=',
                        $this->dateTo.' 23:59:59'
                    )
            )
            ->orderBy(
                $this->sortField,
                $this->sortDirection
            );
    }

    public function render()
    {
        return view(
            'livewire.backoffice.contacts.index',
            [
                'contacts' =>
                    $this->contactsQuery()->paginate(10),
                'statuses' => config(
                    'grabone.contact_statuses',
                    []
                ),
                'activeFilterCount' =>
                    $this->activeFilterCount(),
            ]
        );
    }
}
