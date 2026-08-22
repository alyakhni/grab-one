<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Dashboard
        </h1>

        <p class="mt-2 text-gray-500">
            Overview of Grab One operations.
        </p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-sm font-medium text-gray-500">
                Total Bookings
            </div>

            <div class="mt-3 text-3xl font-bold text-gray-900">
                {{ $totalBookings }}
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-sm font-medium text-gray-500">
                Pending Bookings
            </div>

            <div class="mt-3 text-3xl font-bold text-gray-900">
                {{ $pendingBookings }}
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-sm font-medium text-gray-500">
                Contact Messages
            </div>

            <div class="mt-3 text-3xl font-bold text-gray-900">
                {{ $totalContacts }}
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-sm font-medium text-gray-500">
                Unread Messages
            </div>

            <div class="mt-3 text-3xl font-bold text-gray-900">
                {{ $unreadContacts }}
            </div>
        </div>

    </div>

    <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900">
            Administration
        </h2>

        <p class="mt-2 text-sm leading-6 text-gray-600">
            Manage bookings and customer contact messages using the admin navigation.
        </p>
    </div>
</div>