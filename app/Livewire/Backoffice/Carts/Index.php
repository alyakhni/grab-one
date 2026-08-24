<?php

namespace App\Livewire\Backoffice\Carts;

use App\Models\Cart;
use App\Support\BookingCartSelection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::backoffice')]
#[Title('Fleet | Grab One')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $cartType = '';

    public string $status = '';

    public string $sortField = 'code';

    public string $sortDirection = 'asc';

    protected array $sortableFields = [
        'code',
        'cart_type',
        'operational_status',
        'created_at',
        'updated_at',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCartType(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, $this->sortableFields, true)) {
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

    public function delete(int $cartId): void
    {
        $cart = Cart::query()->findOrFail($cartId);

        if ($cart->assignments()->exists()) {
            session()->flash(
                'error',
                'This cart cannot be deleted because it has booking history.'
            );

            return;
        }

        $cart->delete();

        session()->flash(
            'success',
            'Cart deleted successfully.'
        );
    }

    protected function cartsQuery(): Builder
    {
        $search = trim($this->search);

        $matchingCartTypes = [];

        if ($search !== '') {
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
        }

        return Cart::query()
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
                                    'code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'cart_type',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'operational_status',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'notes',
                                    'like',
                                    "%{$search}%"
                                );

                            if ($matchingCartTypes !== []) {
                                $query->orWhereIn(
                                    'cart_type',
                                    $matchingCartTypes
                                );
                            }
                        }
                    );
                }
            )

            ->when(
                $this->cartType !== '',
                fn (
                    Builder $query
                ): Builder =>
                    $query->where(
                        'cart_type',
                        $this->cartType
                    )
            )

            ->when(
                $this->status !== '',
                fn (
                    Builder $query
                ): Builder =>
                    $query->where(
                        'operational_status',
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
            'livewire.backoffice.carts.index',
            [
                'carts' => $this->cartsQuery()->paginate(15),

                'cartTypes' =>
                    BookingCartSelection::cartTypes(),

                'statuses' => config(
                    'grabone.cart_operational_statuses',
                    []
                ),
            ]
        );
    }
}