<div class="grid gap-6 lg:grid-cols-2">

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Full Name
        </label>

        <input
            type="text"
            wire:model="full_name"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('full_name')
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
            Hotel Name
        </label>

        <input
            type="text"
            wire:model="hotel_name"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('hotel_name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Pickup Location
        </label>

        <select
            wire:model="pickup_location"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >
            @foreach ($pickupLocations as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        @error('pickup_location')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
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
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        @error('cart_type')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Pickup Date & Time
        </label>

        <input
            type="datetime-local"
            wire:model="pickup_date"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('pickup_date')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Return Date & Time
        </label>

        <input
            type="datetime-local"
            wire:model="return_date"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('return_date')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Flight Number
        </label>

        <input
            type="text"
            wire:model="flight_number"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('flight_number')
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

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Total Days
        </label>

        <input
            type="number"
            min="1"
            wire:model="total_days"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('total_days')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Total Price (USD)
        </label>

        <input
            type="number"
            min="0"
            step="0.01"
            wire:model="total_price"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        @error('total_price')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Special Notes
        </label>

        <textarea
            wire:model="special_notes"
            rows="5"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        ></textarea>

        @error('special_notes')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>