<div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Dashboard
        </h1>

        <p class="mt-2 text-gray-500">
            Grab One operations overview.
        </p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">
                Total Bookings
            </div>

            <div class="mt-3 text-3xl font-bold text-gray-900">
                {{ number_format($totalBookings) }}
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">
                Pending Bookings
            </div>

            <div class="mt-3 text-3xl font-bold text-amber-600">
                {{ number_format($pendingBookings) }}
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">
                Contact Messages
            </div>

            <div class="mt-3 text-3xl font-bold text-gray-900">
                {{ number_format($totalContacts) }}
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">
                Unread Messages
            </div>

            <div class="mt-3 text-3xl font-bold text-primary">
                {{ number_format($unreadContacts) }}
            </div>
        </div>

    </div>

    <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-bold text-gray-900">
            Custom Admin Migration
        </h2>

        <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600">
            This backoffice is being built alongside the existing Filament panel.
            Bookings and Contacts will be migrated here before Filament is removed.
        </p>

        <div class="mt-5">
            <a
                href="/admin"
                class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            >
                Open Legacy Filament Admin
            </a>
        </div>

    </div>

</div>