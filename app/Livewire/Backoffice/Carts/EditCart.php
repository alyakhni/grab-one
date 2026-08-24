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
#[Title('Edit Cart | Grab One')]
class EditCart extends Component
{
    public Cart $cart;

    public string $code = '';

    public string $cart_type = '';

    public string $operational_status = 'active';

    public ?string $notes = null;

    public function mount(Cart $cart): void
    {
        $this->cart = $cart;

        $this->code = $cart->code;
        $this->cart_type = $cart->cart_type;
        $this->operational_status =
            $cart->operational_status;
        $this->notes = $cart->notes;
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
                Rule::unique('carts', 'code')
                    ->ignore($this->cart->id),
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

        $this->cart->update($validated);

        session()->flash(
            'success',
            'Cart updated successfully.'
        );

        return $this->redirectRoute(
            'admin.carts.index'
        );
    }

    public function delete()
    {
        if ($this->cart->assignments()->exists()) {
            session()->flash(
                'error',
                'This cart cannot be deleted because it has booking history.'
            );

            return;
        }

        $this->cart->delete();

        session()->flash(
            'success',
            'Cart deleted successfully.'
        );

        return $this->redirectRoute(
            'admin.carts.index'
        );
    }

    public function render()
    {
        return view(
            'livewire.backoffice.carts.edit',
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