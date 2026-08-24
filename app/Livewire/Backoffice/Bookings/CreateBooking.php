<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use App\Support\BookingCartSelection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::backoffice')]
#[Title('Create Booking | Grab One')]
class CreateBooking extends Component
{
    public string $full_name = '';
    public string $email = '';
    public string $phone = '';
    public ?string $hotel_name = null;

    public string $pickup_location = 'hotel';
    public string $pickup_at = '';
    public string $return_at = '';

    public string $cart_selection = '';
    public array $cart_quantities = [];

    public ?string $special_notes = null;
    public ?string $flight_number = null;

    public string $total_price = '0.00';
    public string $status = 'pending';

    public function mount(): void
    {
        $this->cart_selection =
            BookingCartSelection::defaultSelection();

        $this->cart_quantities =
            BookingCartSelection::defaultQuantities();
    }

    public function save()
    {
        $validated = $this->validate(
            $this->rules()
        );

        $itemQuantities =
            BookingCartSelection::itemQuantities(
                $validated['cart_selection'],
                $validated['cart_quantities']
            );

        $bookingData = Arr::except(
            $validated,
            [
                'cart_selection',
                'cart_quantities',
            ]
        );

        $bookingData['hotel_name'] = blank(
            $bookingData['hotel_name'] ?? null
        )
            ? null
            : $bookingData['hotel_name'];

        $bookingData['special_notes'] = blank(
            $bookingData['special_notes'] ?? null
        )
            ? null
            : $bookingData['special_notes'];

        $bookingData['flight_number'] = blank(
            $bookingData['flight_number'] ?? null
        )
            ? null
            : $bookingData['flight_number'];

        DB::transaction(function () use (
            $bookingData,
            $itemQuantities
        ): void {
            $booking = Booking::create(
                $bookingData
            );

            foreach (
                $itemQuantities
                as $cartType => $quantity
            ) {
                $booking
                    ->items()
                    ->create([
                        'cart_type' => $cartType,
                        'quantity' => $quantity,
                    ]);
            }
        });

        session()->flash(
            'success',
            'Booking created successfully.'
        );

        return $this->redirectRoute(
            'admin.bookings.index'
        );
    }

    protected function rules(): array
    {
        return array_merge(
            [
                'full_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                ],

                'phone' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'hotel_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'pickup_location' => [
                    'required',
                    Rule::in(
                        array_keys(
                            config(
                                'grabone.pickup_locations',
                                []
                            )
                        )
                    ),
                ],

                'pickup_at' => [
                    'required',
                    'date',
                ],

                'return_at' => [
                    'required',
                    'date',
                    'after:pickup_at',
                ],

                'cart_selection' => [
                    'required',
                    Rule::in(
                        array_keys(
                            BookingCartSelection::selectionOptions()
                        )
                    ),
                ],

                'cart_quantities' => [
                    'required',
                    'array',
                ],

                'special_notes' => [
                    'nullable',
                    'string',
                ],

                'flight_number' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'total_price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'status' => [
                    'required',
                    Rule::in(
                        array_keys(
                            $this->createStatuses()
                        )
                    ),
                ],
            ],
            BookingCartSelection::quantityRules()
        );
    }

    protected function createStatuses(): array
    {
        $statuses = config(
            'grabone.booking_statuses',
            []
        );

        unset(
            $statuses['confirmed']
        );

        return $statuses;
    }

    public function render()
    {
        return view(
            'livewire.backoffice.bookings.create',
            [
                'pickupLocations' => config(
                    'grabone.pickup_locations',
                    []
                ),

                'cartTypes' =>
                    BookingCartSelection::cartTypes(),

                'selectionOptions' =>
                    BookingCartSelection::selectionOptions(),

                'statuses' =>
                    $this->createStatuses(),

                'showCartAssignments' =>
                    false,

                'availableCartsByType' =>
                    [],
            ]
        );
    }
}