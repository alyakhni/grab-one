<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Grab One Golf Cart Rental')</title>
    
    <meta name="description" content="Rent premium 4-seater and 6-seater golf carts in San Pedro, Belize.">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .hero-bg {
            /* مسار الصورة المباشر من مجلد public */
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/images/best-golf-cart-rental-san-pedro-belize-hero.png');
            background-size: cover;
            background-position: center;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body class="font-sans text-gray-800 bg-white">

    @if(session('success'))
        <div id="toast-success" class="fixed top-28 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-8 py-4 rounded-lg shadow-2xl z-[200] animate-bounce flex items-center gap-3 w-11/12 md:w-auto">
            <i class="fa-solid fa-circle-check text-2xl"></i>
            <span class="font-bold">{{ session('success') }}</span>
            <button onclick="document.getElementById('toast-success').style.display='none'" class="ml-4 text-white hover:text-gray-200  cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div id="toast-error" class="fixed top-28 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-8 py-4 rounded-lg shadow-2xl z-[200] flex items-start gap-3 w-11/12 md:w-auto">
            <i class="fa-solid fa-triangle-exclamation text-2xl mt-1"></i>
            <div class="flex flex-col">
                <span class="font-bold mb-1">Oops! Please check your inputs:</span>
                <ul class="text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button onclick="document.getElementById('toast-error').style.display='none'" class="ml-4 text-white hover:text-gray-200 mt-1  cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.booking-modal')
    @include('partials.scripts')

</body>
</html>