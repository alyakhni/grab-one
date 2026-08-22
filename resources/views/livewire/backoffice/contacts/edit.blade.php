<div>
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Edit Contact #{{ $contact->id }}
            </h1>

            <p class="mt-2 text-gray-500">
                {{ $contact->name }}
            </p>
        </div>

        <a
            href="{{ route('admin.contacts.index') }}"
            class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
        >
            Back to Contacts
        </a>
    </div>

    <form
        wire:submit="save"
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
    >
        @include('livewire.backoffice.contacts.form-fields')

        <div class="mt-8 flex flex-col justify-between gap-4 border-t border-gray-200 pt-6 sm:flex-row">

            <button
                type="button"
                wire:click="delete"
                wire:confirm="Are you sure you want to delete this contact message?"
                class="cursor-pointer rounded-lg border border-red-200 bg-white px-5 py-3 text-sm font-semibold text-red-600 hover:bg-red-50"
            >
                Delete Contact
            </button>

            <div class="flex justify-end gap-3">
                <a
                    href="{{ route('admin.contacts.index') }}"
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
                        Save Changes
                    </span>

                    <span wire:loading wire:target="save">
                        Saving...
                    </span>
                </button>
            </div>

        </div>
    </form>
</div>