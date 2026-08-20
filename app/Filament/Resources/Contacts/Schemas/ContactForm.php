<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select; // استدعاء Select
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                
                // القائمة المنسدلة لاختيار الحالة
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'working_on_it' => 'Working on it',
                        'resolved' => 'Resolved',
                    ])
                    ->required()
                    ->default('pending'),
                
                Toggle::make('is_read')
                    ->label('Mark as Read')
                    ->required(),
            ]);
    }
}