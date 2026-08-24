<div class="grid gap-6 lg:grid-cols-2">

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Cart Code
        </label>

        <input
            type="text"
            wire:model="code"
            placeholder="GO-001"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 uppercase outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        <p class="mt-1 text-xs text-gray-500">
            Unique internal fleet identifier, for example GO-001.
        </p>

        @error('code')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Cart Type
        </label>

        <select
            wire:model="cart_type"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >
            @foreach ($cartTypes as $value => $label)
                <option value="{{ $value }}">
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('cart_type')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Operational Status
        </label>

        <select
            wire:model="operational_status"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}">
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <p class="mt-1 text-xs text-gray-500">
            Reservation is calculated separately from booking schedules.
        </p>

        @error('operational_status')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Internal Notes
        </label>

        <textarea
            wire:model="notes"
            rows="5"
            placeholder="Maintenance details, identifying information, etc."
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        ></textarea>

        @error('notes')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

</div>