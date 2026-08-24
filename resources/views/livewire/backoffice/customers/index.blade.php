<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Customers
        </h1>

        <p class="mt-2 text-gray-500">
            Operational customer records created from booking activity.
            Contacts only appear here when they are linked to an existing customer.
        </p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 p-5">
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                Search
            </label>

            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Name, email, or phone..."
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
            >
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                type="button"
                                wire:click="sortBy('name')"
                                class="inline-flex cursor-pointer items-center gap-1 hover:text-gray-900"
                            >
                                Customer

                                @if ($sortField === 'name')
                                    <span aria-hidden="true">
                                        {!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}
                                    </span>
                                @else
                                    <span class="text-gray-300" aria-hidden="true">
                                        &varr;
                                    </span>
                                @endif
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                type="button"
                                wire:click="sortBy('phone')"
                                class="inline-flex cursor-pointer items-center gap-1 hover:text-gray-900"
                            >
                                Phone

                                @if ($sortField === 'phone')
                                    <span aria-hidden="true">
                                        {!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}
                                    </span>
                                @else
                                    <span class="text-gray-300" aria-hidden="true">
                                        &varr;
                                    </span>
                                @endif
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                type="button"
                                wire:click="sortBy('bookings_count')"
                                class="inline-flex cursor-pointer items-center gap-1 hover:text-gray-900"
                            >
                                Bookings

                                @if ($sortField === 'bookings_count')
                                    <span aria-hidden="true">
                                        {!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}
                                    </span>
                                @else
                                    <span class="text-gray-300" aria-hidden="true">
                                        &varr;
                                    </span>
                                @endif
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                type="button"
                                wire:click="sortBy('bookings_max_pickup_at')"
                                class="inline-flex cursor-pointer items-center gap-1 hover:text-gray-900"
                            >
                                Last Booking

                                @if ($sortField === 'bookings_max_pickup_at')
                                    <span aria-hidden="true">
                                        {!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}
                                    </span>
                                @else
                                    <span class="text-gray-300" aria-hidden="true">
                                        &varr;
                                    </span>
                                @endif
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                type="button"
                                wire:click="sortBy('bookings_sum_total_price')"
                                class="inline-flex cursor-pointer items-center gap-1 hover:text-gray-900"
                            >
                                Booking Value

                                @if ($sortField === 'bookings_sum_total_price')
                                    <span aria-hidden="true">
                                        {!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}
                                    </span>
                                @else
                                    <span class="text-gray-300" aria-hidden="true">
                                        &varr;
                                    </span>
                                @endif
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                type="button"
                                wire:click="sortBy('contacts_count')"
                                class="inline-flex cursor-pointer items-center gap-1 hover:text-gray-900"
                            >
                                Messages

                                @if ($sortField === 'contacts_count')
                                    <span aria-hidden="true">
                                        {!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}
                                    </span>
                                @else
                                    <span class="text-gray-300" aria-hidden="true">
                                        &varr;
                                    </span>
                                @endif
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                type="button"
                                wire:click="sortBy('created_at')"
                                class="inline-flex cursor-pointer items-center gap-1 hover:text-gray-900"
                            >
                                Created

                                @if ($sortField === 'created_at')
                                    <span aria-hidden="true">
                                        {!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}
                                    </span>
                                @else
                                    <span class="text-gray-300" aria-hidden="true">
                                        &varr;
                                    </span>
                                @endif
                            </button>
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($customers as $customer)
                        <tr wire:key="customer-{{ $customer->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $customer->name }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $customer->email ?: 'No email' }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                                {{ $customer->phone ?: 'No phone' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    {{ $customer->bookings_count }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                                @if ($customer->bookings_max_pickup_at)
                                    {{ \Carbon\CarbonImmutable::parse(
                                        $customer->bookings_max_pickup_at,
                                        config('grabone.timezone', 'America/Belize')
                                    )->format('M d, Y') }}
                                @else
                                    <span class="text-gray-400">No bookings</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 font-semibold text-gray-900">
                                ${{ number_format(
                                    (float) ($customer->bookings_sum_total_price ?? 0),
                                    2
                                ) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                                    {{ $customer->contacts_count }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                                {{ $customer->created_at->format('M d, Y') }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <a
                                    href="{{ route('admin.customers.show', $customer) }}"
                                    class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                >
                                    View History
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-sm text-gray-500">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-5">
            {{ $customers->links() }}
        </div>
    </div>
</div>