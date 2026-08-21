<?php

namespace App\Livewire\Backoffice;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::backoffice-auth')]
#[Title('Admin Login | Grab One')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute('backoffice.dashboard');
        }
    }

    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->addError(
                'email',
                "Too many login attempts. Please try again in {$seconds} seconds."
            );

            return null;
        }

        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            RateLimiter::hit($throttleKey, 60);

            $this->addError(
                'email',
                'The provided credentials do not match our records.'
            );

            $this->reset('password');

            return null;
        }

        RateLimiter::clear($throttleKey);

        session()->regenerate();

        return $this->redirectIntended(route('backoffice.dashboard'));
    }

    protected function throttleKey(): string
    {
        return 'backoffice-login:'.Str::lower($this->email).'|'.request()->ip();
    }

    public function render()
    {
        return view('livewire.backoffice.login');
    }
}