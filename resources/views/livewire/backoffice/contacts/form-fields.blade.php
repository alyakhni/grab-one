<div class="grid gap-6 lg:grid-cols-2">

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Name
        </label>

        <input
            type="text"
            wire:model="name"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Email
        </label>

        <input
            type="email"
            wire:model="email"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('email')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Phone
        </label>

        <input
            type="text"
            wire:model="phone"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('phone')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Status
        </label>

        <select
            wire:model="status"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        @error('status')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Message
        </label>

        <textarea
            wire:model="message"
            rows="7"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        ></textarea>

        @error('message')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="inline-flex cursor-pointer items-center gap-3">
            <input
                type="checkbox"
                wire:model="is_read"
                class="h-5 w-5 rounded border-gray-300"
            >

            <span class="text-sm font-semibold text-gray-700">
                Mark as Read
            </span>
        </label>

        @error('is_read')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>