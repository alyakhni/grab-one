<?php

namespace App\Livewire\Backoffice\Bookings;

use App\Models\Booking;
use App\Services\BookingCartAssignmentService;
use App\Services\CustomerIdentityService;
use App\Services\FleetAvailabilityService;
use App\Support\BookingCartSelection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::backoffice')]
#[Title('Edit Booking | Grab One')]
class EditBooking extends Component
{
    public Booking $booking;

    public string $full_name = '';
    public string $email = '';
    public string $phone = '';
    public ?string $hotel_name = null;

    public string $pickup_location = '';
    public string $pickup_at = '';
    public string $return_at = '';

    public string $cart_selection = '';
    public array $cart_quantities = [];

    public array $cart_assignments = [];

    public ?string $special_notes = null;
    public ?string $flight_number = null;

    public string $total_price = '0.00';
    public string $status = 'pending';

    public function mount(
        Booking $booking
    ): void {
        $booking->loadMissing(
            'items.assignments.cart'
        );

        $this->booking =
            $booking;

        $this->full_name =
            $booking->full_name;

        $this->email =
            $booking->email;

        $this->phone =
            $booking->phone;

        $this->hotel_name =
            $booking->hotel_name;

        $this->pickup_location =
            $booking->pickup_location;

        $this->pickup_at =
            $booking->pickup_at
                ->format('Y-m-d\TH:i');

        $this->return_at =
            $booking->return_at
                ->format('Y-m-d\TH:i');

        $this->special_notes =
            $booking->special_notes;

        $this->flight_number =
            $booking->flight_number;

        $this->total_price =
            $booking->total_price;

        $this->status =
            $booking->status;

        $this->cart_quantities =
            BookingCartSelection::defaultQuantities();

        foreach (
            BookingCartSelection::cartTypes()
            as $code => $label
        ) {
            $this->cart_assignments[
                $code
            ] = [];
        }

        foreach (
            $booking->items
            as $item
        ) {
            $this->cart_quantities[
                $item->cart_type
            ] = $item->quantity;

            $this->cart_assignments[
                $item->cart_type
            ] = $item
                ->assignments
                ->pluck('cart_id')
                ->map(
                    fn ($id): string =>
                        (string) $id
                )
                ->values()
                ->all();
        }

        $cartTypes = $booking
            ->items
            ->pluck('cart_type')
            ->values()
            ->all();

        $this->cart_selection =
            count($cartTypes) > 1
                ? 'mix'
                : (
                    $cartTypes[0]
                    ?? BookingCartSelection::defaultSelection()
                );
    }

    public function save()
    {
        $validated = $this->validate(
            $this->rules()
        );

        $itemQuantities = collect(
            BookingCartSelection::itemQuantities(
                $validated['cart_selection'],
                $validated['cart_quantities']
            )
        )
            ->map(
                fn ($quantity): int =>
                    (int) $quantity
            )
            ->sortKeys()
            ->all();

        $selectedTypes =
            array_keys(
                $itemQuantities
            );

        $requestedStatus =
            $validated['status'];

        $preservesAssignmentHistory =
            in_array(
                $requestedStatus,
                [
                    'completed',
                    'cancelled',
                ],
                true
            )
            && $this->booking
                ->items()
                ->whereHas('assignments')
                ->exists();

        if (
            $preservesAssignmentHistory
            && $itemQuantities
                !== $this->currentItemQuantities()
        ) {
            $this->addError(
                'cart_selection',
                'Cart selection and quantities cannot be changed when completing or cancelling a booking with fleet assignment history.'
            );

            return;
        }

        $assignmentData = Arr::only(
            $validated['cart_assignments'] ?? [],
            $selectedTypes
        );

        $bookingData = Arr::except(
            $validated,
            [
                'cart_selection',
                'cart_quantities',
                'cart_assignments',
                'status',
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
            $itemQuantities,
            $selectedTypes,
            $requestedStatus,
            $assignmentData
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

            $this->booking->update(
                $bookingData
            );

            $this->booking
                ->items()
                ->whereNotIn(
                    'cart_type',
                    $selectedTypes
                )
                ->delete();

            foreach (
                $itemQuantities
                as $cartType => $quantity
            ) {
                $this->booking
                    ->items()
                    ->updateOrCreate(
                        [
                            'cart_type' =>
                                $cartType,
                        ],
                        [
                            'quantity' =>
                                $quantity,
                        ]
                    );
            }

            if (
                $requestedStatus
                === 'confirmed'
            ) {
                app(
                    BookingCartAssignmentService::class
                )->confirm(
                    $this->booking->fresh(),
                    $assignmentData
                );

                return;
            }

            $this->booking->update([
                'status' =>
                    $requestedStatus,
            ]);
        });

        session()->flash(
            'success',
            $requestedStatus === 'confirmed'
                ? 'Booking confirmed and fleet assigned successfully.'
                : 'Booking updated successfully.'
        );

        return $this->redirectRoute(
            'admin.bookings.index'
        );
    }

    public function delete()
    {
        $this->booking->delete();

        session()->flash(
            'success',
            'Booking deleted successfully.'
        );

        return $this->redirectRoute(
            'admin.bookings.index'
        );
    }

    protected function currentItemQuantities(): array
    {
        return $this->booking
            ->items()
            ->get([
                'cart_type',
                'quantity',
            ])
            ->mapWithKeys(
                fn ($item): array => [
                    $item->cart_type =>
                        (int) $item->quantity,
                ]
            )
            ->sortKeys()
            ->all();
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

                'cart_assignments' => [
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
                            config(
                                'grabone.booking_statuses',
                                []
                            )
                        )
                    ),
                ],
            ],
            BookingCartSelection::quantityRules()
        );
    }

    protected function availableCartsByType(): array
    {
        if (
            $this->status !== 'confirmed'
            || $this->pickup_at === ''
            || $this->return_at === ''
        ) {
            return [];
        }

        try {
            $itemQuantities =
                BookingCartSelection::itemQuantities(
                    $this->cart_selection,
                    $this->cart_quantities
                );

            $availability = app(
                FleetAvailabilityService::class
            );

            $available = [];

            foreach (
                array_keys($itemQuantities)
                as $cartType
            ) {
                $available[$cartType] =
                    $availability->availableCarts(
                        $cartType,
                        $this->pickup_at,
                        $this->return_at,
                        $this->booking->id
                    );
            }

            return $available;
        } catch (\Throwable) {
            return [];
        }
    }

    public function render()
    {
        return view(
            'livewire.backoffice.bookings.edit',
            [
                'pickupLocations' => config(
                    'grabone.pickup_locations',
                    []
                ),

                'cartTypes' =>
                    BookingCartSelection::cartTypes(),

                'selectionOptions' =>
                    BookingCartSelection::selectionOptions(),

                'statuses' => config(
                    'grabone.booking_statuses',
                    []
                ),

                'showCartAssignments' =>
                    true,

                'availableCartsByType' =>
                    $this->availableCartsByType(),
            ]
        );
    }
}