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
            Cart Selection
        </label>

        <select
            wire:model.live="cart_selection"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >
            @foreach ($selectionOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        @error('cart_selection')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @foreach ($cartTypes as $code => $label)
        @if ($cart_selection === 'mix' || $cart_selection === $code)
            <div wire:key="cart-quantity-{{ $code }}">
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Quantity — {{ $label }}
                </label>

                <input
                    type="number"
                    min="1"
                    max="20"
                    wire:model="cart_quantities.{{ $code }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                >

                @error("cart_quantities.$code")
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif
    @endforeach

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Pickup Date & Time
        </label>

        <input
            type="datetime-local"
            wire:model="pickup_at"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        <p class="mt-1 text-xs text-gray-500">
            Belize time (America/Belize)
        </p>

        @error('pickup_at')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">
            Return Date & Time
        </label>

        <input
            type="datetime-local"
            wire:model="return_at"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        >

        <p class="mt-1 text-xs text-gray-500">
            Belize time (America/Belize)
        </p>

        @error('return_at')
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