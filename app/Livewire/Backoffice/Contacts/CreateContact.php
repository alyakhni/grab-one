<?php

namespace App\Livewire\Backoffice\Contacts;

use App\Models\Contact;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::backoffice')]
#[Title('Create Contact | Grab One')]
class CreateContact extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public string $status = 'pending';

    public bool $is_read = false;

    public function save()
    {
        $validated = $this->validate($this->rules());

        Contact::create($validated);

        session()->flash('success', 'Contact message created successfully.');

        return $this->redirectRoute('admin.contacts.index');
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string'],
            'status' => [
                'required',
                Rule::in(array_keys(config('grabone.contact_statuses', []))),
            ],
            'is_read' => ['boolean'],
        ];
    }

    public function render()
    {
        return view('livewire.backoffice.contacts.create', [
            'statuses' => config('grabone.contact_statuses', []),
        ]);
    }
}