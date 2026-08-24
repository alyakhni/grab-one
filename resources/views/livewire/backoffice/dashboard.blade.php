<div>
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Operational Dashboard
            </h1>

            <p class="mt-2 text-gray-500">
                Live overview of Grab One rentals, requests, and fleet operations.
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm shadow-sm">
            <div class="font-semibold text-gray-900">
                Belize Time
            </div>

            <div class="mt-1 text-gray-500">
                {{ $belizeNow->format('M d, Y - h:i A') }}
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a
            href="{{ route('admin.bookings.index') }}"
            class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition hover:shadow-md"
        >
            <div class="text-sm font-semibold text-amber-800">
                Pending Requests
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $pendingRequests }}
            </div>

            <div class="mt-2 text-xs text-amber-700">
                Requests waiting for review
            </div>
        </a>

        <div class="rounded-2xl border border-green-200 bg-green-50 p-5 shadow-sm">
            <div class="text-sm font-semibold text-green-800">
                Current Rentals
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $currentRentals }}
            </div>

            <div class="mt-2 text-xs text-green-700">
                Confirmed rentals active now
            </div>
        </div>

        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
            <div class="text-sm font-semibold text-blue-800">
                Confirmed Pickups Today
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $confirmedPickupsToday }}
            </div>

            <div class="mt-2 text-xs text-blue-700">
                Based on Belize time
            </div>
        </div>

        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
            <div class="text-sm font-semibold text-indigo-800">
                Returns Today
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $returnsToday }}
            </div>

            <div class="mt-2 text-xs text-indigo-700">
                Confirmed rentals returning today
            </div>
        </div>

        <a
            href="{{ route('admin.carts.index') }}"
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md"
        >
            <div class="text-sm font-semibold text-gray-600">
                Total Fleet
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $totalFleet }}
            </div>

            <div class="mt-2 text-xs text-gray-500">
                All registered carts
            </div>
        </a>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <div class="text-sm font-semibold text-emerald-800">
                Available Now
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $availableNow }}
            </div>

            <div class="mt-2 text-xs text-emerald-700">
                Active carts not rented or in buffer
            </div>
        </div>

        <a
            href="{{ route('admin.carts.index') }}"
            class="rounded-2xl border border-orange-200 bg-orange-50 p-5 shadow-sm transition hover:shadow-md"
        >
            <div class="text-sm font-semibold text-orange-800">
                Maintenance
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $maintenanceCarts }}
            </div>

            <div class="mt-2 text-xs text-orange-700">
                Carts currently unavailable operationally
            </div>
        </a>

        <a
            href="{{ route('admin.contacts.index') }}"
            class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm transition hover:shadow-md"
        >
            <div class="text-sm font-semibold text-violet-800">
                Unread Messages
            </div>

            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $unreadContacts }}
            </div>

            <div class="mt-2 text-xs text-violet-700">
                Contact messages needing attention
            </div>
        </a>
    </div>

    <div class="mt-8 rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">
                    Pending Request Queue
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Upcoming customer requests waiting for confirmation.
                </p>
            </div>

            <a
                href="{{ route('admin.bookings.index') }}"
                class="text-sm font-semibold text-secondary hover:underline"
            >
                View all bookings
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Customer
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Requested Carts
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Pick Up
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Return
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($pendingQueue as $booking)
                        <tr wire:key="pending-dashboard-{{ $booking->id }}" class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $booking->full_name }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $booking->phone }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($booking->items as $item)
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                            {{ $cartTypes[$item->cart_type] ?? $item->cart_type }}
                                            x {{ $item->quantity }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                {{ $booking->pickup_at->format('M d, Y') }}

                                <div class="text-xs text-gray-400">
                                    {{ $booking->pickup_at->format('h:i A') }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                {{ $booking->return_at->format('M d, Y') }}

                                <div class="text-xs text-gray-400">
                                    {{ $booking->return_at->format('h:i A') }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <a
                                    href="{{ route('admin.bookings.edit', $booking) }}"
                                    class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                >
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                No pending booking requests.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        Fleet Schedule
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Actual confirmed cart assignments with exact Belize rental times.
                    </p>

                    <div class="mt-2 text-sm font-semibold text-gray-700">
                        {{ $scheduleWindowLabel }}
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="resetScheduleFilters"
                    class="cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                >
                    Clear Schedule Filters
                </button>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ([
                    'today' => 'Today',
                    'tomorrow' => 'Tomorrow',
                    'specific' => 'Specific Date',
                    'range' => 'Date Range',
                ] as $mode => $label)
                    <button
                        type="button"
                        wire:click="setScheduleMode('{{ $mode }}')"
                        class="cursor-pointer rounded-lg border px-4 py-2 text-sm font-semibold {{ $scheduleMode === $mode ? 'border-secondary bg-secondary text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-3">
                @if ($scheduleMode === 'specific')
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Date
                        </label>

                        <input
                            type="date"
                            wire:model.live="specificDate"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                        >

                        @error('specificDate')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @endif

                @if ($scheduleMode === 'range')
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            From
                        </label>

                        <input
                            type="date"
                            wire:model.live="dateFrom"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                        >

                        @error('dateFrom')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            To
                        </label>

                        <input
                            type="date"
                            wire:model.live="dateTo"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                        >

                        @error('dateTo')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Cart
                    </label>

                    <select
                        wire:model.live="cartId"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                    >
                        <option value="">
                            All carts
                        </option>

                        @foreach ($carts as $cart)
                            <option value="{{ $cart->id }}">
                                {{ $cart->code }}
                                - {{ $cartTypes[$cart->cart_type] ?? $cart->cart_type }}
                                - {{ ucfirst($cart->operational_status) }}
                            </option>
                        @endforeach
                    </select>

                    @error('cartId')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Cart
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Customer
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Pick Up
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Return
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Operationally Free
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Rental State
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($scheduleAssignments as $assignment)
                        @php
                            $booking = $assignment->bookingItem->booking;
                            $cart = $assignment->cart;

                            $isCurrent =
                                $booking->pickup_at->lte($belizeNow)
                                && $booking->return_at->gt($belizeNow);

                            $isUpcoming =
                                $booking->pickup_at->gt($belizeNow);

                            $stateLabel = $isCurrent
                                ? 'Current'
                                : ($isUpcoming ? 'Upcoming' : 'Ended');

                            $stateClass = $isCurrent
                                ? 'bg-green-100 text-green-800'
                                : (
                                    $isUpcoming
                                        ? 'bg-blue-100 text-blue-800'
                                        : 'bg-gray-100 text-gray-700'
                                );

                            $bufferUntil = $booking
                                ->return_at
                                ->copy()
                                ->addMinutes($bufferMinutes);
                        @endphp

                        <tr wire:key="schedule-assignment-{{ $assignment->id }}" class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="font-bold text-gray-900">
                                    {{ $cart->code }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $cartTypes[$cart->cart_type] ?? $cart->cart_type }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $booking->full_name }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    Booking #{{ $booking->id }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                {{ $booking->pickup_at->format('M d, Y') }}

                                <div class="font-semibold text-gray-900">
                                    {{ $booking->pickup_at->format('h:i A') }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                {{ $booking->return_at->format('M d, Y') }}

                                <div class="font-semibold text-gray-900">
                                    {{ $booking->return_at->format('h:i A') }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                {{ $bufferUntil->format('M d, Y') }}

                                <div class="font-semibold text-gray-900">
                                    {{ $bufferUntil->format('h:i A') }}
                                </div>

                                <div class="mt-1 text-xs text-gray-400">
                                    Includes {{ $bufferMinutes }} min buffer
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $stateClass }}">
                                    {{ $stateLabel }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right">
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
                            <td colspan="7" class="px-6 py-14 text-center text-sm text-gray-500">
                                No confirmed cart assignments found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>