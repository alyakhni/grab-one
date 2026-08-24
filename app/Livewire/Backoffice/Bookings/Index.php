<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use App\Support\BookingCartSelection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::backoffice')]
#[Title('Bookings | Grab One')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public array $selected = [];

    protected array $sortableFields = [
        'full_name',
        'pickup_at',
        'return_at',
        'total_price',
        'status',
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

    public function delete(int $bookingId): void
    {
        Booking::query()
            ->findOrFail($bookingId)
            ->delete();

        $this->selected = array_values(
            array_filter(
                $this->selected,
                fn ($id): bool =>
                    (int) $id !== $bookingId
            )
        );

        session()->flash(
            'success',
            'Booking deleted successfully.'
        );
    }

    public function deleteSelected(): void
    {
        $ids = collect($this->selected)
            ->map(
                fn ($id): int => (int) $id
            )
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($ids === []) {
            return;
        }

        Booking::query()
            ->whereIn('id', $ids)
            ->delete();

        $this->selected = [];

        session()->flash(
            'success',
            'Selected bookings deleted successfully.'
        );
    }

    protected function bookingsQuery(): Builder
    {
        $search = trim($this->search);

        $needle = Str::lower($search);

        $matchingCartTypes = collect(
            BookingCartSelection::cartTypes()
        )
            ->filter(
                fn (
                    string $label,
                    string $code
                ): bool =>
                    Str::contains(
                        Str::lower($label),
                        $needle
                    )
                    || Str::contains(
                        Str::lower($code),
                        $needle
                    )
            )
            ->keys()
            ->all();

        return Booking::query()
            ->with('items')

            ->when(
                $search !== '',
                function (
                    Builder $query
                ) use (
                    $search,
                    $matchingCartTypes
                ): void {
                    $query->where(
                        function (
                            Builder $query
                        ) use (
                            $search,
                            $matchingCartTypes
                        ): void {
                            $query
                                ->where(
                                    'full_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'phone',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'status',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'hotel_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'pickup_location',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'flight_number',
                                    'like',
                                    "%{$search}%"
                                );

                            if (
                                $matchingCartTypes !== []
                            ) {
                                $query->orWhereHas(
                                    'items',
                                    fn (
                                        Builder $itemQuery
                                    ): Builder =>
                                        $itemQuery->whereIn(
                                            'cart_type',
                                            $matchingCartTypes
                                        )
                                );
                            }
                        }
                    );
                }
            )

            ->when(
                $this->status !== '',
                fn (
                    Builder $query
                ): Builder =>
                    $query->where(
                        'status',
                        $this->status
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
            'livewire.backoffice.bookings.index',
            [
                'bookings' =>
                    $this->bookingsQuery()
                        ->paginate(10),

                'statuses' => config(
                    'grabone.booking_statuses',
                    []
                ),

                'cartTypes' =>
                    BookingCartSelection::cartTypes(),
            ]
        );
    }
}