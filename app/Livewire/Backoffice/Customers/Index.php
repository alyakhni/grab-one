<?php

namespace App\Livewire\Backoffice\Customers;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::backoffice')]
#[Title('Customers | Grab One')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    protected array $sortableFields = [
        'name',
        'email',
        'phone',
        'bookings_count',
        'bookings_max_pickup_at',
        'bookings_sum_total_price',
        'contacts_count',
        'created_at',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(
        string $field
    ): void {
        if (
            ! in_array(
                $field,
                $this->sortableFields,
                true
            )
        ) {
            return;
        }

        if (
            $this->sortField
            === $field
        ) {
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

    protected function customersQuery(): Builder
    {
        $search = trim(
            $this->search
        );

        return Customer::query()
            ->withCount([
                'bookings',
                'contacts',
            ])
            ->withSum(
                'bookings',
                'total_price'
            )
            ->withMax(
                'bookings',
                'pickup_at'
            )
            ->when(
                $search !== '',
                function (
                    Builder $query
                ) use (
                    $search
                ): void {
                    $query->where(
                        function (
                            Builder $query
                        ) use (
                            $search
                        ): void {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'phone',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email_normalized',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'phone_normalized',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->orderBy(
                $this->sortField,
                $this->sortDirection
            )
            ->orderBy(
                'id',
                'desc'
            );
    }

    public function render()
    {
        return view(
            'livewire.backoffice.customers.index',
            [
                'customers' =>
                    $this->customersQuery()
                        ->paginate(10),
            ]
        );
    }
}