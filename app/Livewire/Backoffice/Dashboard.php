<?php

namespace App\Livewire\Backoffice;

use App\Models\Booking;
use App\Models\BookingCartAssignment;
use App\Models\Cart;
use App\Models\Contact;
use App\Support\BookingCartSelection;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::backoffice')]
#[Title('Dashboard | Grab One')]
class Dashboard extends Component
{
    public string $scheduleMode = 'today';

    public string $specificDate = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $cartId = '';

    public function setScheduleMode(
        string $mode
    ): void {
        if (
            ! in_array(
                $mode,
                [
                    'today',
                    'tomorrow',
                    'specific',
                    'range',
                ],
                true
            )
        ) {
            return;
        }

        $this->resetValidation();

        $this->scheduleMode = $mode;

        $today = $this
            ->businessNow()
            ->toDateString();

        if (
            $mode === 'specific'
            && $this->specificDate === ''
        ) {
            $this->specificDate = $today;
        }

        if (
            $mode === 'range'
            && $this->dateFrom === ''
        ) {
            $this->dateFrom = $today;
        }

        if (
            $mode === 'range'
            && $this->dateTo === ''
        ) {
            $this->dateTo = $this->dateFrom;
        }
    }

    public function updatedSpecificDate(): void
    {
        $this->resetValidation(
            'specificDate'
        );

        if ($this->specificDate === '') {
            return;
        }

        $this->validateOnly(
            'specificDate',
            [
                'specificDate' => [
                    'date_format:Y-m-d',
                ],
            ]
        );
    }

    public function updatedDateFrom(): void
    {
        $this->resetValidation(
            [
                'dateFrom',
                'dateTo',
            ]
        );

        if ($this->dateFrom !== '') {
            $this->validateOnly(
                'dateFrom',
                [
                    'dateFrom' => [
                        'date_format:Y-m-d',
                    ],
                ]
            );
        }

        $this->validateScheduleRange();
    }

    public function updatedDateTo(): void
    {
        $this->resetValidation(
            [
                'dateFrom',
                'dateTo',
            ]
        );

        if ($this->dateTo !== '') {
            $this->validateOnly(
                'dateTo',
                [
                    'dateTo' => [
                        'date_format:Y-m-d',
                    ],
                ]
            );
        }

        $this->validateScheduleRange();
    }

    public function updatedCartId(): void
    {
        $this->resetValidation(
            'cartId'
        );

        if ($this->cartId === '') {
            return;
        }

        $this->validateOnly(
            'cartId',
            [
                'cartId' => [
                    'integer',
                    'exists:carts,id',
                ],
            ]
        );
    }

    public function resetScheduleFilters(): void
    {
        $this->resetValidation();

        $this->scheduleMode = 'today';
        $this->specificDate = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->cartId = '';
    }

    protected function validateScheduleRange(): void
    {
        if (
            $this->scheduleMode !== 'range'
            || $this->dateFrom === ''
            || $this->dateTo === ''
        ) {
            return;
        }

        if (
            $this->dateTo
            < $this->dateFrom
        ) {
            $this->addError(
                'dateTo',
                'The To date must be on or after the From date.'
            );
        }
    }

    protected function businessTimezone(): string
    {
        return (string) config(
            'grabone.timezone',
            config(
                'app.timezone',
                'America/Belize'
            )
        );
    }

    protected function businessNow(): CarbonImmutable
    {
        return CarbonImmutable::now(
            $this->businessTimezone()
        );
    }

    protected function businessDate(
        string $date
    ): CarbonImmutable {
        return CarbonImmutable::parse(
            $date,
            $this->businessTimezone()
        )->startOfDay();
    }

    protected function scheduleWindow(): array
    {
        $now = $this->businessNow();

        if (
            $this->scheduleMode
            === 'tomorrow'
        ) {
            $start = $now
                ->addDay()
                ->startOfDay();

            return [
                $start,
                $start->addDay(),
            ];
        }

        if (
            $this->scheduleMode
            === 'specific'
        ) {
            $date = $this->specificDate !== ''
                ? $this->specificDate
                : $now->toDateString();

            $start = $this->businessDate(
                $date
            );

            return [
                $start,
                $start->addDay(),
            ];
        }

        if (
            $this->scheduleMode
            === 'range'
        ) {
            $from = $this->dateFrom !== ''
                ? $this->dateFrom
                : $now->toDateString();

            $to = $this->dateTo !== ''
                ? $this->dateTo
                : $from;

            $start = $this->businessDate(
                $from
            );

            if ($to < $from) {
                return [
                    $start,
                    $start,
                ];
            }

            $end = $this
                ->businessDate($to)
                ->addDay();

            return [
                $start,
                $end,
            ];
        }

        $start = $now->startOfDay();

        return [
            $start,
            $start->addDay(),
        ];
    }

    protected function scheduleWindowLabel(): string
    {
        [$start, $end] =
            $this->scheduleWindow();

        if (
            $this->scheduleMode
            === 'today'
        ) {
            return 'Today - '.$start->format(
                'M d, Y'
            );
        }

        if (
            $this->scheduleMode
            === 'tomorrow'
        ) {
            return 'Tomorrow - '.$start->format(
                'M d, Y'
            );
        }

        if (
            $this->scheduleMode
            === 'specific'
        ) {
            return $start->format(
                'M d, Y'
            );
        }

        if ($start->equalTo($end)) {
            return 'Invalid date range';
        }

        return $start->format(
            'M d, Y'
        ).' - '.$end
            ->subDay()
            ->format('M d, Y');
    }

    protected function availableNowCount(
        CarbonImmutable $now
    ): int {
        $bufferMinutes = (int) config(
            'grabone.booking_buffer_minutes',
            60
        );

        $bufferThreshold =
            $now->subMinutes(
                $bufferMinutes
            );

        return Cart::query()
            ->where(
                'operational_status',
                'active'
            )
            ->whereDoesntHave(
                'assignments.bookingItem.booking',
                function (
                    Builder $query
                ) use (
                    $now,
                    $bufferThreshold
                ): void {
                    $query
                        ->where(
                            'status',
                            'confirmed'
                        )
                        ->where(
                            'pickup_at',
                            '<=',
                            $now->toDateTimeString()
                        )
                        ->where(
                            'return_at',
                            '>',
                            $bufferThreshold
                                ->toDateTimeString()
                        );
                }
            )
            ->count();
    }

    protected function scheduleAssignments(): Collection
    {
        [$start, $end] =
            $this->scheduleWindow();

        if ($start->equalTo($end)) {
            return collect();
        }

        return BookingCartAssignment::query()
            ->with([
                'cart',
                'bookingItem.booking',
            ])
            ->whereHas(
                'bookingItem.booking',
                function (
                    Builder $query
                ) use (
                    $start,
                    $end
                ): void {
                    $query
                        ->where(
                            'status',
                            'confirmed'
                        )
                        ->where(
                            'pickup_at',
                            '<',
                            $end->toDateTimeString()
                        )
                        ->where(
                            'return_at',
                            '>',
                            $start->toDateTimeString()
                        );
                }
            )
            ->when(
                $this->cartId !== '',
                fn (
                    Builder $query
                ): Builder =>
                    $query->where(
                        'cart_id',
                        (int) $this->cartId
                    )
            )
            ->get()
            ->sortBy(
                function (
                    BookingCartAssignment $assignment
                ): string {
                    $booking =
                        $assignment
                            ->bookingItem
                            ->booking;

                    return $booking
                        ->pickup_at
                        ->format('YmdHis')
                        .'-'
                        .$assignment
                            ->cart
                            ->code;
                }
            )
            ->values();
    }

    public function render()
    {
        $now = $this->businessNow();

        $todayStart =
            $now->startOfDay();

        $tomorrowStart =
            $todayStart->addDay();

        return view(
            'livewire.backoffice.dashboard',
            [
                'belizeNow' =>
                    $now,

                'pendingRequests' =>
                    Booking::query()
                        ->where(
                            'status',
                            'pending'
                        )
                        ->count(),

                'currentRentals' =>
                    Booking::query()
                        ->where(
                            'status',
                            'confirmed'
                        )
                        ->where(
                            'pickup_at',
                            '<=',
                            $now->toDateTimeString()
                        )
                        ->where(
                            'return_at',
                            '>',
                            $now->toDateTimeString()
                        )
                        ->count(),

                'confirmedPickupsToday' =>
                    Booking::query()
                        ->where(
                            'status',
                            'confirmed'
                        )
                        ->where(
                            'pickup_at',
                            '>=',
                            $todayStart
                                ->toDateTimeString()
                        )
                        ->where(
                            'pickup_at',
                            '<',
                            $tomorrowStart
                                ->toDateTimeString()
                        )
                        ->count(),

                'returnsToday' =>
                    Booking::query()
                        ->where(
                            'status',
                            'confirmed'
                        )
                        ->where(
                            'return_at',
                            '>=',
                            $todayStart
                                ->toDateTimeString()
                        )
                        ->where(
                            'return_at',
                            '<',
                            $tomorrowStart
                                ->toDateTimeString()
                        )
                        ->count(),

                'totalFleet' =>
                    Cart::query()->count(),

                'availableNow' =>
                    $this->availableNowCount(
                        $now
                    ),

                'maintenanceCarts' =>
                    Cart::query()
                        ->where(
                            'operational_status',
                            'maintenance'
                        )
                        ->count(),

                'unreadContacts' =>
                    Contact::query()
                        ->where(
                            'is_read',
                            false
                        )
                        ->count(),

                'pendingQueue' =>
                    Booking::query()
                        ->with('items')
                        ->where(
                            'status',
                            'pending'
                        )
                        ->orderBy(
                            'pickup_at'
                        )
                        ->limit(6)
                        ->get(),

                'scheduleAssignments' =>
                    $this->scheduleAssignments(),

                'scheduleWindowLabel' =>
                    $this
                        ->scheduleWindowLabel(),

                'carts' =>
                    Cart::query()
                        ->orderBy('code')
                        ->get([
                            'id',
                            'code',
                            'cart_type',
                            'operational_status',
                        ]),

                'cartTypes' =>
                    BookingCartSelection::cartTypes(),

                'bufferMinutes' =>
                    (int) config(
                        'grabone.booking_buffer_minutes',
                        60
                    ),
            ]
        );
    }
}