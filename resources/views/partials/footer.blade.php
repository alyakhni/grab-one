<footer class="bg-dark text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
            <div class="flex items-center gap-2 mb-4">
                <img src="{{ asset('images/grab-one-golf-cart-rental-san-pedro-logo-top.png') }}"
                    alt="Grab One Golf Carts in Ambergris Caye, Belize" 
                    class="h-[5em] w-auto object-contain rounded drop-shadow-md mix-blend-screen"
                >
            </div>
            <p class="text-gray-400 text-sm">Providing the best and safest transportation on Ambergris Caye. Rent with us and enjoy every minute of your adventure.</p>
        </div>
        <div>
            <h4 class="text-lg font-bold mb-4 border-b border-gray-700 pb-2 inline-block">Quick Links</h4>
            <ul class="space-y-2 text-sm text-gray-400">
                <li><a href="#home" class="hover:text-primary transition">Home</a></li>
                <li><a href="#fleet" class="hover:text-primary transition">Our Fleet</a></li>
                <li><a href="#contact" class="hover:text-primary transition">Contact Us</a></li>
                <li><a href="#" onclick="openModal()" class="hover:text-primary transition">Book a Cart</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-lg font-bold mb-4 border-b border-gray-700 pb-2 inline-block">Follow Us</h4>
            <div class="flex space-x-4">
                <a href="https://www.facebook.com/profile.php?id=100083232185587" target="_blank" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-pink-600 transition">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-8 border-t border-gray-800 text-center text-sm text-gray-500">
        © {{ date('Y') }} Grab One Golf Cart Rental. All Rights Reserved.
    </div>
</footer>

<a href="https://wa.me/5016289698" target="_blank" class="fixed bottom-6 right-6 w-14 h-14 bg-green-500 text-white rounded-full flex items-center justify-center text-3xl shadow-xl hover:bg-green-600 hover:scale-110 transition duration-300 z-40">
    <i class="fa-brands fa-whatsapp"></i>
</a>