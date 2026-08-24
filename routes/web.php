<?php

use App\Http\Controllers\Backoffice\LogoutController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Livewire\Backoffice\Bookings\CreateBooking;
use App\Livewire\Backoffice\Bookings\EditBooking;
use App\Livewire\Backoffice\Bookings\Index as BookingsIndex;
use App\Livewire\Backoffice\Carts\CreateCart;
use App\Livewire\Backoffice\Carts\EditCart;
use App\Livewire\Backoffice\Carts\Index as CartsIndex;
use App\Livewire\Backoffice\Contacts\CreateContact;
use App\Livewire\Backoffice\Contacts\EditContact;
use App\Livewire\Backoffice\Contacts\Index as ContactsIndex;
use App\Livewire\Backoffice\Customers\Index as CustomersIndex;
use App\Livewire\Backoffice\Customers\Show as CustomerShow;
use App\Livewire\Backoffice\Dashboard;
use App\Livewire\Backoffice\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/book-now', [BookingController::class, 'store'])
    ->name('booking.store');

Route::post('/contact-us', [ContactController::class, 'send'])
    ->name('contact.send');

Route::livewire('/admin/login', Login::class)
    ->name('login');

Route::prefix('admin')
    ->name('admin.')
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

        Route::livewire('/carts', CartsIndex::class)
            ->name('carts.index');

        Route::livewire('/carts/create', CreateCart::class)
            ->name('carts.create');

        Route::livewire('/carts/{cart}/edit', EditCart::class)
            ->name('carts.edit');

        Route::livewire('/customers', CustomersIndex::class)
            ->name('customers.index');

        Route::livewire('/customers/{customer}', CustomerShow::class)
            ->name('customers.show');

        Route::livewire('/contacts', ContactsIndex::class)
            ->name('contacts.index');

        Route::livewire('/contacts/create', CreateContact::class)
            ->name('contacts.create');

        Route::livewire('/contacts/{contact}/edit', EditContact::class)
            ->name('contacts.edit');

        Route::post('/logout', LogoutController::class)
            ->name('logout');
    });