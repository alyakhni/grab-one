<div>
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Fleet
            </h1>

            <p class="mt-2 text-gray-500">
                Manage the actual golf carts operated by Grab One.
            </p>
        </div>

        <a
            href="{{ route('admin.carts.create') }}"
            class="inline-flex rounded-lg bg-secondary px-5 py-3 text-sm font-semibold text-white hover:opacity-90"
        >
            Add Cart
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

        <div class="grid gap-4 border-b border-gray-200 p-5 lg:grid-cols-[1fr_220px_220px]">

            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Search
                </label>

                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cart code, type, status, notes..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                >
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Cart Type
                </label>

                <select
                    wire:model.live="cartType"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                >
                    <option value="">
                        All cart types
                    </option>

                    @foreach ($cartTypes as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Operational Status
                </label>

                <select
                    wire:model.live="status"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                >
                    <option value="">
                        All statuses
                    </option>

                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                wire:click="sortBy('code')"
                                class="cursor-pointer"
                            >
                                Cart
                            </button>
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                wire:click="sortBy('cart_type')"
                                class="cursor-pointer"
                            >
                                Type
                            </button>
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button
                                wire:click="sortBy('operational_status')"
                                class="cursor-pointer"
                            >
                                Operational Status
                            </button>
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Notes
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse ($carts as $cart)

                        <tr
                            wire:key="cart-{{ $cart->id }}"
                            class="hover:bg-gray-50"
                        >
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="font-bold text-gray-900">
                                    {{ $cart->code }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                {{ $cartTypes[$cart->cart_type] ?? $cart->cart_type }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @php
                                    $statusClass = match ($cart->operational_status) {
                                        'active' => 'bg-green-100 text-green-800',
                                        'maintenance' => 'bg-amber-100 text-amber-800',
                                        'inactive' => 'bg-gray-200 text-gray-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statuses[$cart->operational_status] ?? ucfirst($cart->operational_status) }}
                                </span>
                            </td>

                            <td class="max-w-md px-5 py-4 text-sm text-gray-600">
                                {{ $cart->notes ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex justify-end gap-2">

                                    <a
                                        href="{{ route('admin.carts.edit', $cart) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                    >
                                        Edit
                                    </a>

                                    <button
                                        type="button"
                                        wire:click="delete({{ $cart->id }})"
                                        wire:confirm="Are you sure you want to delete this cart?"
                                        class="cursor-pointer rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50"
                                    >
                                        Delete
                                    </button>

                                </div>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-6 py-16 text-center text-sm text-gray-500"
                            >
                                No fleet carts found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-5">
            {{ $carts->links() }}
        </div>

    </div>
</div>