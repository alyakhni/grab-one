<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Admin Login | Grab One' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100 font-sans text-gray-900">

    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        {{ $slot }}
    </main>

    @livewireScripts

</body>
</html>