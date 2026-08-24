<?php

namespace App\Livewire\Backoffice\Contacts;

use App\Models\Contact;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::backoffice')]
#[Title('Edit Contact | Grab One')]
class EditContact extends Component
{
    public Contact $contact;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public string $status = 'pending';

    public bool $is_read = false;

    public function mount(Contact $contact): void
    {
        $this->contact = $contact;

        $this->name = $contact->name;
        $this->email = $contact->email;
        $this->phone = $contact->phone;
        $this->message = $contact->message;
        $this->status = $contact->status;
        $this->is_read = $contact->is_read;
    }

    public function save()
    {
        $validated = $this->validate($this->rules());

        $this->contact->update($validated);

        session()->flash('success', 'Contact message updated successfully.');

        return $this->redirectRoute('admin.contacts.index');
    }

    public function delete()
    {
        $this->contact->delete();

        session()->flash('success', 'Contact message deleted successfully.');

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
        return view('livewire.backoffice.contacts.edit', [
            'statuses' => config('grabone.contact_statuses', []),
        ]);
    }
}