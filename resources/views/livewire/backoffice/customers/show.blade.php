<div>
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <a
                href="{{ route('admin.customers.index') }}"
                class="text-sm font-semibold text-secondary hover:underline"
            >
                Back to Customers
            </a>

            <h1 class="mt-3 text-3xl font-bold text-gray-900">
                {{ $customer->name }}
            </h1>

            <p class="mt-2 text-gray-500">
                Customer operational history and linked activity.
                Rental times are shown in Belize time.
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                Customer ID
            </div>

            <div class="mt-1 font-bold text-gray-900">
                #{{ $customer->id }}
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-gray-500">
                Total Bookings
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $bookingCount }}
            </div>
        </div>

        <div class="rounded-2xl border border-green-200 bg-green-50 p-5 shadow-sm">
            <div class="text-sm font-medium text-green-700">
                Confirmed
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $confirmedBookingCount }}
            </div>
        </div>

        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
            <div class="text-sm font-medium text-blue-700">
                Completed
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $completedBookingCount }}
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-gray-500">
                Booking Value
            </div>

            <div class="mt-2 text-2xl font-bold text-gray-900">
                ${{ number_format($totalBookingValue, 2) }}
            </div>
        </div>

        <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm">
            <div class="text-sm font-medium text-violet-700">
                Messages
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $contactCount }}
            </div>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <div class="text-sm font-medium text-amber-700">
                Unread
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $unreadContactCount }}
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900">
                Customer Profile
            </h2>

            <dl class="mt-5 space-y-4 text-sm">
                <div>
                    <dt class="font-semibold text-gray-500">
                        Name
                    </dt>

                    <dd class="mt-1 text-gray-900">
                        {{ $customer->name }}
                    </dd>
                </div>

                <div>
                    <dt class="font-semibold text-gray-500">
                        Email
                    </dt>

                    <dd class="mt-1 text-gray-900">
                        {{ $customer->email ?: 'Not recorded' }}
                    </dd>
                </div>

                <div>
                    <dt class="font-semibold text-gray-500">
                        Phone
                    </dt>

                    <dd class="mt-1 text-gray-900">
                        {{ $customer->phone ?: 'Not recorded' }}
                    </dd>
                </div>

                <div>
                    <dt class="font-semibold text-gray-500">
                        Customer Since
                    </dt>

                    <dd class="mt-1 text-gray-900">
                        {{ $customer->created_at->format('M d, Y') }}
                    </dd>
                </div>

                <div>
                    <dt class="font-semibold text-gray-500">
                        Internal Notes
                    </dt>

                    <dd class="mt-1 whitespace-pre-line text-gray-900">
                        {{ $customer->notes ?: 'No internal notes.' }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900">
                Latest Booking
            </h2>

            @if ($lastBooking)
                <div class="mt-5">
                    <div class="text-lg font-bold text-gray-900">
                        Booking #{{ $lastBooking->id }}
                    </div>

                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Pick Up
                            </div>

                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $lastBooking->pickup_at->format('M d, Y - h:i A') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Return
                            </div>

                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $lastBooking->return_at->format('M d, Y - h:i A') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Status
                            </div>

                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $bookingStatuses[$lastBooking->status] ?? ucfirst($lastBooking->status) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Price
                            </div>

                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                ${{ number_format((float) $lastBooking->total_price, 2) }}
                            </div>
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.bookings.edit', $lastBooking) }}"
                        class="mt-5 inline-flex rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Open Booking
                    </a>
                </div>
            @else
                <p class="mt-5 text-sm text-gray-500">
                    This customer has no linked bookings.
                </p>
            @endif

            <p class="mt-5 border-t border-gray-100 pt-4 text-xs leading-5 text-gray-400">
                Booking Value is the sum of recorded booking prices.
                It is not a payment or revenue ledger.
            </p>
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 p-5">
            <h2 class="text-lg font-bold text-gray-900">
                Booking History
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                All bookings linked to this customer record.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Booking
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Requested Carts
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Actual Fleet
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Pick Up
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Return
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Price
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($bookings as $booking)
                        <tr wire:key="customer-booking-{{ $booking->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div class="font-bold text-gray-900">
                                    Booking #{{ $booking->id }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $pickupLocations[$booking->pickup_location] ?? $booking->pickup_location }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    @forelse ($booking->items as $item)
                                        <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                            {{ $cartTypes[$item->cart_type] ?? $item->cart_type }}
                                            x {{ $item->quantity }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400">
                                            No cart items
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $hasAssignments = false;
                                    @endphp

                                    @foreach ($booking->items as $item)
                                        @foreach ($item->assignments as $assignment)
                                            @if ($assignment->cart)
                                                @php
                                                    $hasAssignments = true;
                                                @endphp

                                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                                    {{ $assignment->cart->code }}
                                                </span>
                                            @endif
                                        @endforeach
                                    @endforeach

                                    @if (! $hasAssignments)
                                        <span class="text-xs text-gray-400">
                                            Not assigned
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                                {{ $booking->pickup_at->format('M d, Y') }}

                                <div class="text-xs text-gray-400">
                                    {{ $booking->pickup_at->format('h:i A') }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                                {{ $booking->return_at->format('M d, Y') }}

                                <div class="text-xs text-gray-400">
                                    {{ $booking->return_at->format('h:i A') }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 font-semibold text-gray-900">
                                ${{ number_format((float) $booking->total_price, 2) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                @php
                                    $bookingStatusClass = match ($booking->status) {
                                        'pending' => 'bg-amber-100 text-amber-800',
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $bookingStatusClass }}">
                                    {{ $bookingStatuses[$booking->status] ?? ucfirst($booking->status) }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <a
                                    href="{{ route('admin.bookings.edit', $booking) }}"
                                    class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                >
                                    Booking
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-14 text-center text-sm text-gray-500">
                                No linked bookings.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-5">
            {{ $bookings->links() }}
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 p-5">
            <h2 class="text-lg font-bold text-gray-900">
                Contact History
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Contact messages clearly linked to this customer identity.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Received
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Message
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Read
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($contacts as $contact)
                        <tr wire:key="customer-contact-{{ $contact->id }}" class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                                {{ $contact->created_at->format('M d, Y') }}

                                <div class="text-xs text-gray-400">
                                    {{ $contact->created_at->format('h:i A') }}
                                </div>
                            </td>

                            <td class="max-w-xl px-4 py-4 text-sm text-gray-700">
                                {{ $contact->message }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                @php
                                    $contactStatusClass = match ($contact->status) {
                                        'pending' => 'bg-red-100 text-red-800',
                                        'working_on_it' => 'bg-amber-100 text-amber-800',
                                        'resolved' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $contactStatusClass }}">
                                    {{ $contactStatuses[$contact->status] ?? ucfirst($contact->status) }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                @if ($contact->is_read)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                        Read
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                        Unread
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <a
                                    href="{{ route('admin.contacts.edit', $contact) }}"
                                    class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                >
                                    Contact
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center text-sm text-gray-500">
                                No linked contact messages.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-5">
            {{ $contacts->links() }}
        </div>
    </div>
</div>