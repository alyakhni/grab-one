<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\CustomerIdentityService;
use App\Support\BookingCartSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'hotel_name' => ['nullable', 'string', 'max:255'],

            'pickup_location' => [
                'required',
                Rule::in(
                    array_keys(
                        config('grabone.pickup_locations', [])
                    )
                ),
            ],

            'pickup_at' => ['required', 'date'],

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

            'cart_quantities' => ['required', 'array'],

            'special_notes' => ['nullable', 'string'],
        ];

        $rules = array_merge(
            $rules,
            BookingCartSelection::quantityRules()
        );

        $validated = $request->validate($rules);

        $itemQuantities = BookingCartSelection::itemQuantities(
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

        $bookingData['status'] = 'pending';
        $bookingData['total_price'] = 0.00;

        DB::transaction(function () use (
            $bookingData,
            $itemQuantities
        ): void {
            $customer = app(
                CustomerIdentityService::class
            )->resolveForBooking(
                $bookingData['full_name'],
                $bookingData['email'],
                $bookingData['phone']
            );

            $bookingData['customer_id'] =
                $customer?->id;

            $booking = Booking::create(
                $bookingData
            );

            foreach ($itemQuantities as $cartType => $quantity) {
                $booking->items()->create([
                    'cart_type' => $cartType,
                    'quantity' => $quantity,
                ]);
            }
        });

        return redirect()
            ->back()
            ->with(
                'success',
                'Your booking request has been submitted! We will contact you shortly to confirm availability, pricing, and details.'
            );
    }
}