<div>
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Add Cart
            </h1>

            <p class="mt-2 text-gray-500">
                Add an actual golf cart to the Grab One fleet.
            </p>
        </div>

        <a
            href="{{ route('admin.carts.index') }}"
            class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
        >
            Back to Fleet
        </a>
    </div>

    <form
        wire:submit="save"
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
    >
        @include('livewire.backoffice.carts.form-fields')

        <div class="mt-8 flex justify-end gap-3">
            <a
                href="{{ route('admin.carts.index') }}"
                class="rounded-lg border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50"
            >
                Cancel
            </a>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="cursor-pointer rounded-lg bg-secondary px-5 py-3 text-sm font-semibold text-white hover:opacity-90 disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="save">
                    Add Cart
                </span>

                <span wire:loading wire:target="save">
                    Saving...
                </span>
            </button>
        </div>
    </form>
</div>