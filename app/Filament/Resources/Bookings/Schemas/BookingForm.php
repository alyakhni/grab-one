<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select; // تأكد من استدعاء Select
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('hotel_name')
                    ->maxLength(255) // تم إزالة tel() وإضافة حد أقصى للنص
                    ->default(null),
                
                // تحويل مكان الاستلام إلى Select
                Select::make('pickup_location')
                    ->options([
                        'My Hotel' => 'My Hotel',
                        'San Pedro Airport' => 'San Pedro Airport',
                        'Water Taxi Terminal' => 'Water Taxi Terminal',
                        'In-Store' => 'In-Store',
                    ])
                    ->required()
                    ->native(false),

                DateTimePicker::make('pickup_date')
                    ->required(),
                DateTimePicker::make('return_date')
                    ->required(),
                
                // تحويل نوع العربة إلى Select
                Select::make('cart_type')
                    ->options([
                        '4-Seater' => '4-Seater Cart',
                        '6-Seater' => '6-Seater Cart',
                    ])
                    ->required()
                    ->native(false),

                Textarea::make('special_notes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('flight_number')
                    ->default(null),
                TextInput::make('total_days')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                Select::make('status')
                    ->label('Booking Status')
                    ->options([
                        'pending' => 'Pending',     // قيد الانتظار
                        'confirmed' => 'Confirmed', // مؤكد (جاري أو سيبدأ قريباً)
                        'completed' => 'Completed', // مكتمل / تم إرجاع العربة
                        'cancelled' => 'Cancelled', // ملغي
                    ])
                    ->required()
                    ->default('pending')
                    ->native(false),
            ]);
    }
}