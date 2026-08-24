<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Grab One Admin' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100 font-sans text-gray-900">

<div class="min-h-screen lg:flex">

    <aside class="border-b border-gray-200 bg-white lg:min-h-screen lg:w-64 lg:border-b-0 lg:border-r">
        <div class="flex items-center justify-between px-6 py-5">
            <div>
                <div class="text-xl font-bold text-secondary">
                    Grab One
                </div>

                <div class="text-xs font-medium uppercase tracking-widest text-gray-400">
                    Admin
                </div>
            </div>
        </div>

        <nav class="flex gap-2 overflow-x-auto px-4 pb-4 lg:block lg:space-y-2">

            <a
                href="{{ route('admin.dashboard') }}"
                class="block whitespace-nowrap rounded-lg px-4 py-3 text-sm font-semibold
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-secondary text-white'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-secondary' }}"
            >
                Dashboard
            </a>

            <a
                href="{{ route('admin.bookings.index') }}"
                class="block whitespace-nowrap rounded-lg px-4 py-3 text-sm font-semibold
                    {{ request()->routeIs('admin.bookings.*')
                        ? 'bg-secondary text-white'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-secondary' }}"
            >
                Bookings
            </a>

            <a
                href="{{ route('admin.carts.index') }}"
                class="block whitespace-nowrap rounded-lg px-4 py-3 text-sm font-semibold
                    {{ request()->routeIs('admin.carts.*')
                        ? 'bg-secondary text-white'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-secondary' }}"
            >
                Fleet
            </a>

            <a
                href="{{ route('admin.customers.index') }}"
                class="block whitespace-nowrap rounded-lg px-4 py-3 text-sm font-semibold
                    {{ request()->routeIs('admin.customers.*')
                        ? 'bg-secondary text-white'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-secondary' }}"
            >
                Customers
            </a>

            <a
                href="{{ route('admin.contacts.index') }}"
                class="block whitespace-nowrap rounded-lg px-4 py-3 text-sm font-semibold
                    {{ request()->routeIs('admin.contacts.*')
                        ? 'bg-secondary text-white'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-secondary' }}"
            >
                Contacts
            </a>

        </nav>
    </aside>

    <div class="min-w-0 flex-1">

        <header class="border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-8">

                <div>
                    <div class="text-sm text-gray-500">
                        Signed in as
                    </div>

                    <div class="font-semibold text-gray-900">
                        {{ auth()->user()->name }}
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        Logout
                    </button>
                </form>

            </div>
        </header>

        <main class="p-5 sm:p-8">
            {{ $slot }}
        </main>

    </div>

</div>

@livewireScripts

</body>
</html>
