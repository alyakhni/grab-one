<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use App\Support\BookingCartSelection;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
    public string $cartType = '';
    public string $pickupLocation = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public string $draftStatus = '';
    public string $draftCartType = '';
    public string $draftPickupLocation = '';
    public string $draftDateFrom = '';
    public string $draftDateTo = '';

    public bool $filtersOpen = false;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public array $selected = [];

    protected array $sortableFields = [
        'full_name',
        'phone',
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

    public function openFilters(): void
    {
        $this->draftStatus = $this->status;
        $this->draftCartType = $this->cartType;
        $this->draftPickupLocation = $this->pickupLocation;
        $this->draftDateFrom = $this->dateFrom;
        $this->draftDateTo = $this->dateTo;

        $this->resetValidation();

        $this->filtersOpen = true;
    }

    public function closeFilters(): void
    {
        $this->filtersOpen = false;

        $this->resetValidation();
    }

    public function applyFilters(): void
    {
        $this->validate([
            'draftStatus' => [
                'nullable',
                Rule::in(
                    array_keys(
                        config(
                            'grabone.booking_statuses',
                            []
                        )
                    )
                ),
            ],

            'draftCartType' => [
                'nullable',
                Rule::in(
                    array_keys(
                        BookingCartSelection::cartTypes()
                    )
                ),
            ],

            'draftPickupLocation' => [
                'nullable',
                Rule::in(
                    array_keys(
                        config(
                            'grabone.pickup_locations',
                            []
                        )
                    )
                ),
            ],

            'draftDateFrom' => [
                'nullable',
                'date',
            ],

            'draftDateTo' => [
                'nullable',
                'date',
                'after_or_equal:draftDateFrom',
            ],
        ]);

        $this->status = $this->draftStatus;
        $this->cartType = $this->draftCartType;
        $this->pickupLocation = $this->draftPickupLocation;
        $this->dateFrom = $this->draftDateFrom;
        $this->dateTo = $this->draftDateTo;

        $this->filtersOpen = false;

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->status = '';
        $this->cartType = '';
        $this->pickupLocation = '';
        $this->dateFrom = '';
        $this->dateTo = '';

        $this->draftStatus = '';
        $this->draftCartType = '';
        $this->draftPickupLocation = '';
        $this->draftDateFrom = '';
        $this->draftDateTo = '';

        $this->filtersOpen = false;

        $this->resetValidation();

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

    protected function activeFilterCount(): int
    {
        $count = 0;

        foreach ([
            $this->status,
            $this->cartType,
            $this->pickupLocation,
        ] as $value) {
            if ($value !== '') {
                $count++;
            }
        }

        if (
            $this->dateFrom !== ''
            || $this->dateTo !== ''
        ) {
            $count++;
        }

        return $count;
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

        $timezone = (string) config(
            'grabone.timezone',
            config(
                'app.timezone',
                'America/Belize'
            )
        );

        $dateFrom = $this->dateFrom !== ''
            ? CarbonImmutable::parse(
                $this->dateFrom,
                $timezone
            )
                ->startOfDay()
                ->format('Y-m-d H:i:s')
            : null;

        $dateTo = $this->dateTo !== ''
            ? CarbonImmutable::parse(
                $this->dateTo,
                $timezone
            )
                ->endOfDay()
                ->format('Y-m-d H:i:s')
            : null;

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

            ->when(
                $this->cartType !== '',
                fn (
                    Builder $query
                ): Builder =>
                    $query->whereHas(
                        'items',
                        fn (
                            Builder $itemQuery
                        ): Builder =>
                            $itemQuery->where(
                                'cart_type',
                                $this->cartType
                            )
                    )
            )

            ->when(
                $this->pickupLocation !== '',
                fn (
                    Builder $query
                ): Builder =>
                    $query->where(
                        'pickup_location',
                        $this->pickupLocation
                    )
            )

            ->when(
                $dateFrom !== null,
                fn (
                    Builder $query
                ): Builder =>
                    $query->where(
                        'return_at',
                        '>=',
                        $dateFrom
                    )
            )

            ->when(
                $dateTo !== null,
                fn (
                    Builder $query
                ): Builder =>
                    $query->where(
                        'pickup_at',
                        '<=',
                        $dateTo
                    )
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

                'pickupLocations' => config(
                    'grabone.pickup_locations',
                    []
                ),

                'activeFilterCount' =>
                    $this->activeFilterCount(),
            ]
        );
    }
}
