<?php

namespace App\Livewire\Backoffice;

use App\Models\Booking;
use App\Models\Contact;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::backoffice')]
#[Title('Dashboard | Grab One')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.backoffice.dashboard', [
            'totalBookings' => Booking::count(),
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'totalContacts' => Contact::count(),
            'unreadContacts' => Contact::where('is_read', false)->count(),
        ]);
    }
}