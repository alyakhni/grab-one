<div class="w-full max-w-md">

    <div class="mb-8 text-center">
        <a
            href="/"
            class="inline-block text-3xl font-bold text-secondary"
        >
            Grab One
        </a>

        <p class="mt-2 text-sm text-gray-500">
            Backoffice Administration
        </p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-7 shadow-sm sm:p-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Sign in
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Enter your administrator credentials.
            </p>
        </div>

        <form wire:submit="login" class="space-y-5">

            <div>
                <label
                    for="email"
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    Email
                </label>

                <input
                    wire:model="email"
                    id="email"
                    type="email"
                    autocomplete="email"
                    autofocus
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                >

                @error('email')
                    <p class="mt-2 text-sm font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label
                    for="password"
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    Password
                </label>

                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/20"
                >

                @error('password')
                    <p class="mt-2 text-sm font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-3 text-sm text-gray-600">
                <input
                    wire:model="remember"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300"
                >

                Remember me
            </label>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                class="w-full cursor-pointer rounded-lg bg-secondary px-4 py-3 font-semibold text-white transition hover:opacity-90 disabled:cursor-wait disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="login">
                    Sign in
                </span>

                <span wire:loading wire:target="login">
                    Signing in...
                </span>
            </button>

        </form>

    </div>

    <div class="mt-6 text-center">
        <a
            href="/"
            class="text-sm font-medium text-gray-500 hover:text-secondary"
        >
            ← Back to public website
        </a>
    </div>

</div>