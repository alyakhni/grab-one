<section id="fleet" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-dark">Our Golf Cart Fleet</h2>
            <div class="w-16 h-1 bg-primary mx-auto mt-4"></div>
            <p class="mt-4 text-gray-600">Choose the perfect size for your family or group.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            
            <div id="book-4-seater" class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="h-64 bg-gray-200 relative">
                    <img src="{{ asset('images/fleet/4-seater-deluxe-golf-cart-san-pedro.png') }}" alt="4 Seater Deluxe Golf Cart Rental Belize" class="w-full h-full object-cover">
                    <div class="absolute top-4 right-4 bg-primary text-white text-sm font-bold px-3 py-1 rounded">Popular</div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-2">4-Seater Deluxe Cart</h3>
                    <p class="text-gray-600 mb-4">Perfect for couples and small families exploring San Pedro. Lifted, rugged tires, and comfortable seating.</p>
                    <ul class="text-sm text-gray-600 mb-6 space-y-2">
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Fits up to 4 passengers</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Gas Powered (Full tank included)</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Free Bridge Pass</li>
                    </ul>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-2xl font-bold text-dark">Contact for Price</span>
                        <button onclick="openModal('4-Seater')" class="bg-secondary hover:bg-blue-900 text-white px-6 py-2 rounded font-semibold transition cursor-pointer">Reserve Now</button>
                    </div>
                </div>
            </div>

            <div id="book-6-seater" class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="h-64 bg-gray-200 relative">
                    <img src="{{ asset('images/fleet/6-seater-family-golf-cart-ambergris-caye.png') }}" alt="6 Seater Family Golf Cart Rental" class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-2">6-Seater Family Cart</h3>
                    <p class="text-gray-600 mb-4">Ideal for larger groups or families. Extended roof, extra space, and smooth ride for the whole crew.</p>
                    <ul class="text-sm text-gray-600 mb-6 space-y-2">
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Fits up to 6 passengers</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Extended Roof & Storage</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Free Bridge Pass</li>
                    </ul>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-2xl font-bold text-dark">Contact for Price</span>
                        <button onclick="openModal('6-Seater')" class="bg-secondary hover:bg-blue-900 text-white px-6 py-2 rounded font-semibold transition cursor-pointer">Reserve Now</button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>