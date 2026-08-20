<nav class="bg-brand-bg shadow-sm fixed w-full z-50 top-0 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-2">
            
            <div class="flex items-center shrink-0">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('images/grab-one-golf-cart-rental-san-pedro-logo-top-2.png') }}" 
                         alt="Grab One Golf Cart Rental San Pedro Belize" 
                         class="h-16 md:h-24 w-auto mix-blend-multiply transition-all">
                </a>
            </div>
            
            <div class="hidden md:flex items-center space-x-8">
                <a href="#home" class="text-secondary hover:text-primary font-bold transition">Home</a>
                <a href="#fleet" class="text-secondary hover:text-primary font-bold transition">Our Fleet</a>
                <a href="#contact" class="text-secondary hover:text-primary font-bold transition">Contact</a>
                
                <button onclick="openModal()" id="book-now-top" class="bg-primary hover:bg-red-700 text-white px-6 py-2 rounded-md font-bold transition shadow-md flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-calendar-check"></i> Book Now
                </button>
            </div>

            <div class="md:hidden flex items-center">
                <button onclick="toggleMenu()" class="text-secondary focus:outline-none cursor-pointer">
                    <i class="fa-solid fa-bars-staggered text-3xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>