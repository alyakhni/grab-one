<div>
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Bookings
            </h1>

            <p class="mt-2 text-gray-500">
                Manage Grab One booking requests.
                All rental times are shown in Belize time.
            </p>
        </div>

        <a
            href="{{ route('admin.bookings.create') }}"
            class="inline-flex rounded-lg bg-secondary px-5 py-3 text-sm font-semibold text-white hover:opacity-90"
        >
            Create Booking
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

        <div class="grid gap-4 border-b border-gray-200 p-5 lg:grid-cols-[1fr_240px_auto]">
            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Search
                </label>

                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Name, phone, email, hotel, cart..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                >
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Status
                </label>

                <select
                    wire:model.live="status"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                >
                    <option value="">All statuses</option>

                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                @if (count($selected) > 0)
                    <button
                        type="button"
                        wire:click="deleteSelected"
                        wire:confirm="Delete all selected bookings?"
                        class="w-full cursor-pointer rounded-lg border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50"
                    >
                        Delete Selected ({{ count($selected) }})
                    </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-4 py-3"></th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('full_name')" class="cursor-pointer">
                                Customer
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Phone
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Carts
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('pickup_at')" class="cursor-pointer">
                                Pick Up
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('return_at')" class="cursor-pointer">
                                Return
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('total_price')" class="cursor-pointer">
                                Price
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('status')" class="cursor-pointer">
                                Status
                            </button>
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($bookings as $booking)
                        <tr wire:key="booking-{{ $booking->id }}" class="hover:bg-gray-50">

                            <td class="px-4 py-4">
                                <input
                                    type="checkbox"
                                    value="{{ $booking->id }}"
                                    wire:model.live="selected"
                                    class="h-4 w-4 rounded border-gray-300"
                                >
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $booking->full_name }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $booking->email }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                                {{ $booking->phone }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    @forelse ($booking->items as $item)
                                        <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                            {{ $cartTypes[$item->cart_type] ?? $item->cart_type }}
                                            × {{ $item->quantity }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400">
                                            No cart items
                                        </span>
                                    @endforelse
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
                                    $statusClass = match ($booking->status) {
                                        'pending' => 'bg-amber-100 text-amber-800',
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statuses[$booking->status] ?? ucfirst($booking->status) }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a
                                        href="{{ route('admin.bookings.edit', $booking) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                    >
                                        Edit
                                    </a>

                                    <button
                                        type="button"
                                        wire:click="delete({{ $booking->id }})"
                                        wire:confirm="Are you sure you want to delete this booking?"
                                        class="cursor-pointer rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center text-sm text-gray-500">
                                No bookings found.
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
</div>