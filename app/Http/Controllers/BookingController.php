<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // 1. التحقق من البيانات المرسلة
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'hotel_name' => 'nullable|string|max:255',
            'pickup_location' => 'required|string|max:255',
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after:pickup_date',
            'cart_type' => 'required|string',
            'special_notes' => 'nullable|string',
        ]);

        // 2. حساب إجمالي عدد الأيام (Total Days)
        $pickupDate = Carbon::parse($validated['pickup_date']);
        $returnDate = Carbon::parse($validated['return_date']);
        $totalDays = $pickupDate->diffInDays($returnDate);
        
        // التأكد من أن الحد الأدنى هو يوم واحد
        $validated['total_days'] = $totalDays > 0 ? $totalDays : 1;
        
        // 3. إعطاء قيم افتراضية للحقول الباقية
        $validated['status'] = 'pending';
        $validated['total_price'] = 0.00; // الإدمن سيقوم بتحديث السعر من لوحة الإدارة

        // 4. الحفظ في قاعدة البيانات
        Booking::create($validated);

        // 5. العودة للموقع مع رسالة نجاح
        return redirect()->back()->with('success', 'Your booking request has been submitted! We will contact you shortly to confirm pricing and details.');
    }
}