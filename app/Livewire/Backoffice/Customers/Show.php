<?php

namespace App\Livewire\Backoffice\Customers;

use App\Models\Booking;
use App\Models\Contact;
use App\Models\Customer;
use App\Support\BookingCartSelection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::backoffice')]
#[Title('Customer History | Grab One')]
class Show extends Component
{
    use WithPagination;

    public Customer $customer;

    public function mount(
        Customer $customer
    ): void {
        $this->customer =
            $customer;
    }

    public function render()
    {
        $bookingQuery =
            Booking::query()
                ->where(
                    'customer_id',
                    $this->customer->id
                );

        $contactQuery =
            Contact::query()
                ->where(
                    'customer_id',
                    $this->customer->id
                );

        return view(
            'livewire.backoffice.customers.show',
            [
                'bookingCount' =>
                    (clone $bookingQuery)
                        ->count(),

                'confirmedBookingCount' =>
                    (clone $bookingQuery)
                        ->where(
                            'status',
                            'confirmed'
                        )
                        ->count(),

                'completedBookingCount' =>
                    (clone $bookingQuery)
                        ->where(
                            'status',
                            'completed'
                        )
                        ->count(),

                'totalBookingValue' =>
                    (float) (
                        (clone $bookingQuery)
                            ->sum('total_price')
                    ),

                'contactCount' =>
                    (clone $contactQuery)
                        ->count(),

                'unreadContactCount' =>
                    (clone $contactQuery)
                        ->where(
                            'is_read',
                            false
                        )
                        ->count(),

                'lastBooking' =>
                    (clone $bookingQuery)
                        ->orderByDesc(
                            'pickup_at'
                        )
                        ->first(),

                'bookings' =>
                    (clone $bookingQuery)
                        ->with([
                            'items.assignments.cart',
                        ])
                        ->orderByDesc(
                            'pickup_at'
                        )
                        ->paginate(
                            10,
                            ['*'],
                            'bookingsPage'
                        ),

                'contacts' =>
                    (clone $contactQuery)
                        ->orderByDesc(
                            'created_at'
                        )
                        ->paginate(
                            10,
                            ['*'],
                            'contactsPage'
                        ),

                'bookingStatuses' =>
                    config(
                        'grabone.booking_statuses',
                        []
                    ),

                'contactStatuses' =>
                    config(
                        'grabone.contact_statuses',
                        []
                    ),

                'pickupLocations' =>
                    config(
                        'grabone.pickup_locations',
                        []
                    ),

                'cartTypes' =>
                    BookingCartSelection::cartTypes(),
            ]
        );
    }
}