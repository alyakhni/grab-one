<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter; // استدعاء الفلتر
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // === الأعمدة الظاهرة دائماً (الأهم) ===
                
                TextColumn::make('full_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->tooltip('Click to copy'),

                TextColumn::make('cart_type')
                    ->badge()
                    ->searchable(),

                TextColumn::make('pickup_date')
                    ->label('Pick Up') 
                    ->date('M d, Y h:i A')
                    ->sortable(),

                TextColumn::make('return_date')
                    ->label('Return') 
                    ->date('M d, Y h:i A')
                    ->sortable(),

                TextColumn::make('total_price')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',   // أصفر
                        'confirmed' => 'success', // أخضر
                        'completed' => 'info',    // أزرق 
                        'cancelled' => 'danger',  // أحمر
                        default => 'gray',
                    })
                    ->searchable(),

                // === الأعمدة المخفية افتراضياً (لترتيب الشاشة) ===
                
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('hotel_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pickup_location')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('flight_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_days')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // === إضافة فلتر الحالة هنا ===
                SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}