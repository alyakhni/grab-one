<?php

use App\Http\Controllers\Backoffice\LogoutController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Livewire\Backoffice\Bookings\CreateBooking;
use App\Livewire\Backoffice\Bookings\EditBooking;
use App\Livewire\Backoffice\Bookings\Index as BookingsIndex;
use App\Livewire\Backoffice\Contacts\CreateContact;
use App\Livewire\Backoffice\Contacts\EditContact;
use App\Livewire\Backoffice\Contacts\Index as ContactsIndex;
use App\Livewire\Backoffice\Dashboard;
use App\Livewire\Backoffice\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/book-now', [BookingController::class, 'store'])->name('booking.store');
Route::post('/contact-us', [ContactController::class, 'send'])->name('contact.send');

Route::livewire('/backoffice/login', Login::class)
    ->name('login');

Route::prefix('backoffice')
    ->name('backoffice.')
    ->middleware('auth')
    ->group(function (): void {
        Route::livewire('/', Dashboard::class)
            ->name('dashboard');

        Route::livewire('/bookings', BookingsIndex::class)
            ->name('bookings.index');

        Route::livewire('/bookings/create', CreateBooking::class)
            ->name('bookings.create');

        Route::livewire('/bookings/{booking}/edit', EditBooking::class)
            ->name('bookings.edit');

        Route::livewire('/contacts', ContactsIndex::class)
            ->name('contacts.index');

        Route::livewire('/contacts/create', CreateContact::class)
            ->name('contacts.create');

        Route::livewire('/contacts/{contact}/edit', EditContact::class)
            ->name('contacts.edit');

        Route::post('/logout', LogoutController::class)
            ->name('logout');
    });