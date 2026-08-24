<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\CustomerIdentityService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:50',
            ],

            'message' => [
                'required',
                'string',
            ],
        ]);

        $customer = app(
            CustomerIdentityService::class
        )->findExistingForContact(
            $validated['email'],
            $validated['phone']
        );

        $validated['customer_id'] =
            $customer?->id;

        $validated['is_read'] = false;
        $validated['status'] = 'pending';

        Contact::create(
            $validated
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Thank you! Your message has been sent successfully. We will get back to you soon.'
            );
    }
}