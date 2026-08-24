@php
    $pickupLocations = config('grabone.pickup_locations', []);
    $fleetCarts = config('fleet.carts', []);
    $defaultQuantity = max(1, (int) config('grabone.default_cart_quantity', 1));
@endphp

<div id="bookingModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex hidden items-center justify-center px-4">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden modal-enter" id="modalContent">

        <div class="bg-dark text-white px-6 py-4 flex justify-between items-center shrink-0">
            <h3 class="text-xl font-bold">Book Your Golf Cart</h3>

            <button onclick="closeModal()" class="text-gray-300 hover:text-white cursor-pointer">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar flex-1 min-h-0">
            <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary"
                            placeholder="Full Name"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Email Address
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary"
                            placeholder="Email Address"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Phone Number / WhatsApp
                        </label>

                        <input
                            type="tel"
                            name="phone"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary"
                            placeholder="Phone Number"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Hotel staying in San Pedro
                        </label>

                        <input
                            type="text"
                            name="hotel_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary"
                            placeholder="Hotel Name"
                        >
                    </div>

                </div>

                <h4 class="font-bold text-dark border-b pb-2 mb-4">
                    Rental Details
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Pick Up Date & Time
                        </label>

                        <input
                            type="datetime-local"
                            name="pickup_at"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary cursor-pointer"
                            required
                        >

                        <p class="mt-1 text-xs text-gray-500">
                            Belize local time
                        </p>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Return Date & Time
                        </label>

                        <input
                            type="datetime-local"
                            name="return_at"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary cursor-pointer"
                            required
                        >

                        <p class="mt-1 text-xs text-gray-500">
                            Belize local time
                        </p>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Pick Up Location
                        </label>

                        <select
                            name="pickup_location"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary cursor-pointer"
                            required
                        >
                            @foreach ($pickupLocations as $code => $label)
                                <option value="{{ $code }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Golf Cart Selection
                        </label>

                        <select
                            id="cartSelectionSelect"
                            name="cart_selection"
                            onchange="syncCartQuantityFields()"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary cursor-pointer"
                            required
                        >
                            @foreach ($fleetCarts as $code => $cart)
                                <option value="{{ $code }}">
                                    {{ $cart['booking_label'] ?? $cart['name'] }}
                                </option>
                            @endforeach

                            @if (count($fleetCarts) > 1)
                                <option value="mix">
                                    Mix
                                </option>
                            @endif
                        </select>
                    </div>

                    @foreach ($fleetCarts as $code => $cart)
                        <div
                            id="cartQuantity-{{ $code }}"
                            data-cart-quantity="{{ $code }}"
                            class="{{ $loop->first ? '' : 'hidden' }}"
                        >
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Quantity — {{ $cart['booking_label'] ?? $cart['name'] }}
                            </label>

                            <input
                                type="number"
                                name="cart_quantities[{{ $code }}]"
                                min="1"
                                max="20"
                                value="{{ $defaultQuantity }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary"
                            >
                        </div>
                    @endforeach

                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Your Message (optional)
                    </label>

                    <textarea
                        name="special_notes"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary"
                        rows="3"
                        placeholder="Any special requests?"
                    ></textarea>
                </div>

            </form>
        </div>

        <div class="p-4 border-t flex justify-end gap-3 bg-gray-50 shrink-0">

            <button
                type="button"
                onclick="closeModal()"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition cursor-pointer"
            >
                Cancel
            </button>

            <button
                type="submit"
                form="bookingForm"
                class="px-6 py-2 bg-primary text-white rounded hover:bg-red-700 transition font-bold shadow cursor-pointer"
            >
                Submit Booking
            </button>

        </div>
    </div>
</div>