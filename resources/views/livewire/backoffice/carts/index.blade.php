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
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 lg:flex-row lg:items-end">
            <div class="flex-1">
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

            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    wire:click="openFilters"
                    class="cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    Filters{{ $activeFilterCount > 0 ? " ({$activeFilterCount})" : '' }}
                </button>

                @if ($activeFilterCount > 0)
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                    >
                        Clear Filters
                    </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button type="button" wire:click="sortBy('code')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Cart</span>
                                <span>
                                    @if ($sortField === 'code')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button type="button" wire:click="sortBy('cart_type')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Type</span>
                                <span>
                                    @if ($sortField === 'cart_type')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button type="button" wire:click="sortBy('operational_status')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Operational Status</span>
                                <span>
                                    @if ($sortField === 'operational_status')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button type="button" wire:click="sortBy('notes')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Notes</span>
                                <span>
                                    @if ($sortField === 'notes')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($carts as $cart)
                        <tr wire:key="cart-{{ $cart->id }}" class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="font-bold text-gray-900">{{ $cart->code }}</div>
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
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-gray-500">
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

    @if ($showFilters)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            wire:click.self="closeFilters"
        >
            <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Fleet Filters</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Narrow the fleet list without cluttering the table.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeFilters"
                        class="cursor-pointer rounded-lg px-3 py-2 text-gray-500 hover:bg-gray-100"
                    >
                        Close
                    </button>
                </div>

                <div class="grid gap-5 p-6">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Cart Type
                        </label>

                        <select
                            wire:model="filterCartType"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                        >
                            <option value="">All cart types</option>

                            @foreach ($cartTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        @error('filterCartType')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Operational Status
                        </label>

                        <select
                            wire:model="filterStatus"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                        >
                            <option value="">All statuses</option>

                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        @error('filterStatus')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 px-6 py-5 sm:flex-row sm:justify-between">
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="cursor-pointer rounded-lg border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                    >
                        Clear Filters
                    </button>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            wire:click="closeFilters"
                            class="cursor-pointer rounded-lg border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            wire:click="applyFilters"
                            class="cursor-pointer rounded-lg bg-secondary px-5 py-3 text-sm font-semibold text-white hover:opacity-90"
                        >
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
