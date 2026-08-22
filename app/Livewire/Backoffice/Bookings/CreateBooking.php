<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
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
    public string $pickup_location = 'My Hotel';
    public string $pickup_date = '';
    public string $return_date = '';
    public string $cart_type = '4-Seater';
    public ?string $special_notes = null;
    public ?string $flight_number = null;
    public int $total_days = 1;
    public string $total_price = '0.00';
    public string $status = 'pending';

    public function save()
    {
        $validated = $this->validate($this->rules());

        $validated['hotel_name'] = blank($validated['hotel_name'] ?? null)
            ? null
            : $validated['hotel_name'];

        $validated['special_notes'] = blank($validated['special_notes'] ?? null)
            ? null
            : $validated['special_notes'];

        $validated['flight_number'] = blank($validated['flight_number'] ?? null)
            ? null
            : $validated['flight_number'];

        Booking::create($validated);

        session()->flash('success', 'Booking created successfully.');

        return $this->redirectRoute('backoffice.bookings.index');
    }

    protected function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'hotel_name' => ['nullable', 'string', 'max:255'],
            'pickup_location' => [
                'required',
                Rule::in(array_keys(Booking::PICKUP_LOCATIONS)),
            ],
            'pickup_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after:pickup_date'],
            'cart_type' => [
                'required',
                Rule::in(array_keys(Booking::CART_TYPES)),
            ],
            'special_notes' => ['nullable', 'string'],
            'flight_number' => ['nullable', 'string', 'max:255'],
            'total_days' => ['required', 'integer', 'min:1'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'status' => [
                'required',
                Rule::in(array_keys(Booking::STATUSES)),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.backoffice.bookings.create', [
            'pickupLocations' => Booking::PICKUP_LOCATIONS,
            'cartTypes' => Booking::CART_TYPES,
            'statuses' => Booking::STATUSES,
        ]);
    }
}