<div>
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Contacts
            </h1>

            <p class="mt-2 text-gray-500">
                Manage customer contact messages.
            </p>
        </div>

        <a
            href="{{ route('admin.contacts.create') }}"
            class="inline-flex rounded-lg bg-secondary px-5 py-3 text-sm font-semibold text-white hover:opacity-90"
        >
            Create Contact
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 xl:flex-row xl:items-end">
            <div class="flex-1">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Search
                </label>

                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Name, email, phone, message..."
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

                @if (count($selected) > 0)
                    <button
                        type="button"
                        wire:click="deleteSelected"
                        wire:confirm="Delete all selected contact messages?"
                        class="cursor-pointer rounded-lg border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50"
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
                            <button type="button" wire:click="sortBy('name')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Customer</span>
                                <span>
                                    @if ($sortField === 'name')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button type="button" wire:click="sortBy('phone')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Phone</span>
                                <span>
                                    @if ($sortField === 'phone')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button type="button" wire:click="sortBy('message')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Message</span>
                                <span>
                                    @if ($sortField === 'message')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button type="button" wire:click="sortBy('status')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Status</span>
                                <span>
                                    @if ($sortField === 'status')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button type="button" wire:click="sortBy('is_read')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Read</span>
                                <span>
                                    @if ($sortField === 'is_read')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <button type="button" wire:click="sortBy('created_at')" class="inline-flex cursor-pointer items-center gap-1">
                                <span>Received</span>
                                <span>
                                    @if ($sortField === 'created_at')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @else
                                        ↕
                                    @endif
                                </span>
                            </button>
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($contacts as $contact)
                        <tr
                            wire:key="contact-{{ $contact->id }}"
                            class="{{ $contact->is_read ? 'hover:bg-gray-50' : 'bg-amber-50/40 hover:bg-amber-50' }}"
                        >
                            <td class="px-4 py-4">
                                <input
                                    type="checkbox"
                                    value="{{ $contact->id }}"
                                    wire:model.live="selected"
                                    class="h-4 w-4 rounded border-gray-300"
                                >
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $contact->name }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $contact->email }}
                                </div>

                                @if (in_array($contact->id, $identityReviewIds, true))
                                    <div class="mt-2">
                                        <span
                                            title="Email and phone point to conflicting or ambiguous customer records."
                                            class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700"
                                        >
                                            Identity Review
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                                {{ $contact->phone }}
                            </td>

                            <td class="max-w-xs px-4 py-4 text-sm text-gray-600">
                                <div class="line-clamp-2">
                                    {{ $contact->message }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                @php
                                    $statusClass = match ($contact->status) {
                                        'pending' => 'bg-red-100 text-red-800',
                                        'working_on_it' => 'bg-amber-100 text-amber-800',
                                        'resolved' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statuses[$contact->status] ?? $contact->status }}
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

                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600">
                                {{ $contact->created_at->format('M d, Y') }}

                                <div class="text-xs text-gray-400">
                                    {{ $contact->created_at->format('h:i A') }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="toggleRead({{ $contact->id }})"
                                        class="cursor-pointer rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                    >
                                        {{ $contact->is_read ? 'Mark Unread' : 'Mark Read' }}
                                    </button>

                                    <a
                                        href="{{ route('admin.contacts.edit', $contact) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                    >
                                        Edit
                                    </a>

                                    <button
                                        type="button"
                                        wire:click="delete({{ $contact->id }})"
                                        wire:confirm="Are you sure you want to delete this contact message?"
                                        class="cursor-pointer rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-sm text-gray-500">
                                No contact messages found.
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

    @if ($showFilters)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            wire:click.self="closeFilters"
        >
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Contact Filters</h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Filter messages by workflow status, read state, and received date.
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

                <div class="grid gap-5 p-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Status
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

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Read Status
                        </label>

                        <select
                            wire:model="filterReadStatus"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                        >
                            <option value="">All messages</option>
                            <option value="read">Read</option>
                            <option value="unread">Unread</option>
                        </select>

                        @error('filterReadStatus')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Received From
                        </label>

                        <input
                            type="date"
                            wire:model="filterDateFrom"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                        >

                        @error('filterDateFrom')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Received To
                        </label>

                        <input
                            type="date"
                            wire:model="filterDateTo"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                        >

                        @error('filterDateTo')
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
