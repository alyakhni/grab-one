<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/book-now', [BookingController::class, 'store'])->name('booking.store');
Route::post('/contact-us', [ContactController::class, 'send'])->name('contact.send');