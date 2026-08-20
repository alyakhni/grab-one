<section id="contact" class="py-20 bg-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-dark">Get in Touch</h2>
            <div class="w-16 h-1 bg-primary mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-xl font-bold mb-6">Send us a Message</h3>
                <form action="{{ route('contact.send') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Full Name</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary" type="text" id="name" name="name" placeholder="John Doe" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email Address</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary" type="email" id="email" name="email" placeholder="john@example.com" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">Phone / WhatsApp</label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary" type="tel" id="phone" name="phone" placeholder="+1 234 567 8900" required>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="message">Your Message</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary" id="message" name="message" rows="4" placeholder="How can we help you?" required></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-dark hover:bg-gray-800 text-white font-bold py-3 px-4 rounded transition shadow-lg cursor-pointer">
                        Send Message
                    </button>
                </form>
            </div>

            <div class="flex flex-col justify-between">
                <div class="mb-8">
                    <p class="text-gray-600 mb-6">
                        You can reach us easily via phone or WhatsApp. We are located near El Pescador, San Pedro.
                    </p>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-100 text-primary rounded-full flex items-center justify-center mr-4">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-dark">Phone</h4>
                            <a href="tel:+5016289698" class="text-gray-600 hover:text-primary">+501 628-9698</a><br>
                            <a href="tel:+5016666609" class="text-gray-600 hover:text-primary">+501 666-6609</a>
                        </div>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-100 text-primary rounded-full flex items-center justify-center mr-4">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-dark">Email</h4>
                            <a href="mailto:grabonegolfcart@gmail.com" class="text-gray-600 hover:text-primary">grabonegolfcart@gmail.com</a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 text-primary rounded-full flex items-center justify-center mr-4">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-dark">Location</h4>
                            <p class="text-gray-600">El Pescador, San Pedro, Belize</p>
                        </div>
                    </div>
                </div>
                
                <div class="h-64 w-full rounded-lg overflow-hidden shadow-md">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3796.192216425809!2d-87.9617252!3d17.923186499999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f5c9bd583d8a8d7%3A0x4a4f098e186d0bfe!2sGrabone%20Golf%20Cart%20Rental!5e0!3m2!1sen!2slb!4v1775500758718!5m2!1sen!2slb" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>