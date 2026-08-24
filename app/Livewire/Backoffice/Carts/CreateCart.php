<?php

namespace App\Livewire\Backoffice\Carts;

use App\Models\Cart;
use App\Support\BookingCartSelection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::backoffice')]
#[Title('Create Cart | Grab One')]
class CreateCart extends Component
{
    public string $code = '';

    public string $cart_type = '';

    public string $operational_status = 'active';

    public ?string $notes = null;

    public function mount(): void
    {
        $this->cart_type =
            array_key_first(
                BookingCartSelection::cartTypes()
            ) ?? '';
    }

    public function save()
    {
        $this->code = Str::upper(
            trim($this->code)
        );

        $validated = $this->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('carts', 'code'),
            ],

            'cart_type' => [
                'required',
                Rule::in(
                    array_keys(
                        BookingCartSelection::cartTypes()
                    )
                ),
            ],

            'operational_status' => [
                'required',
                Rule::in(
                    array_keys(
                        config(
                            'grabone.cart_operational_statuses',
                            []
                        )
                    )
                ),
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['notes'] = blank(
            $validated['notes'] ?? null
        )
            ? null
            : trim($validated['notes']);

        Cart::create($validated);

        session()->flash(
            'success',
            'Cart created successfully.'
        );

        return $this->redirectRoute(
            'admin.carts.index'
        );
    }

    public function render()
    {
        return view(
            'livewire.backoffice.carts.create',
            [
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