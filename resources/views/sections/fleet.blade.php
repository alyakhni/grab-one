<section id="fleet" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-dark">
                Our Golf Cart Fleet
            </h2>

            <div class="w-16 h-1 bg-primary mx-auto mt-4"></div>

            <p class="mt-4 text-gray-600">
                Choose the perfect size for your family or group.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

            @foreach (config('fleet.carts', []) as $code => $cart)

                <div
                    id="{{ $cart['anchor'] }}"
                    class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100"
                >
                    <div class="h-64 bg-gray-200 relative">

                        <img
                            src="{{ asset($cart['image']) }}"
                            alt="{{ $cart['alt'] }}"
                            class="w-full h-full object-cover"
                        >

                        @if (! empty($cart['badge']))
                            <div class="absolute top-4 right-4 bg-primary text-white text-sm font-bold px-3 py-1 rounded">
                                {{ $cart['badge'] }}
                            </div>
                        @endif

                    </div>

                    <div class="p-6">

                        <h3 class="text-2xl font-bold mb-2">
                            {{ $cart['name'] }}
                        </h3>

                        <p class="text-gray-600 mb-4">
                            {{ $cart['description'] }}
                        </p>

                        <ul class="text-sm text-gray-600 mb-6 space-y-2">
                            @foreach ($cart['features'] as $feature)
                                <li>
                                    <i class="fa-solid fa-check text-green-500 mr-2"></i>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        <div class="flex justify-between items-center mt-4">

                            <span class="text-2xl font-bold text-dark">
                                {{ $cart['price_label'] }}
                            </span>

                            <button
                                onclick="openModal('{{ $code }}')"
                                class="bg-secondary hover:bg-blue-900 text-white px-6 py-2 rounded font-semibold transition cursor-pointer"
                            >
                                Reserve Now
                            </button>

                        </div>
                    </div>
                </div>

            @endforeach

        </div>
    </div>
</section>