<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_reference')
                ->label('Booking Ref')
                ->searchable()
                ->copyable(),

            TextColumn::make('user.first_name')
                ->label('User')
                ->searchable()
                ->sortable(),

            TextColumn::make('booking_type')
                ->label('Type')
                ->formatStateUsing(fn ($state) => ucfirst($state)),

            TextColumn::make('booking_date')
                ->label('Date')
                ->dateTime()
                ->sortable(),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn ($state) => match($state) {
                    'pending' => 'warning',
                    'confirmed' => 'success',
                    'cancelled' => 'danger',
                    'completed' => 'gray',
                    default => 'secondary',
                })
                ->formatStateUsing(fn ($state) => ucfirst($state)),

            TextColumn::make('total_price')
                ->label('Total')
                ->money('USD'),

            TextColumn::make('discount_amount')
                ->label('Discount')
                ->money('USD'),

                TextColumn::make('payment_status')
                ->label('Payment')
                ->badge()
                ->color(fn ($state) => match($state) {
                    'pending' => 'warning',
                    'paid' => 'success',
                    'refunded' => 'gray',
                    'failed' => 'danger',
                    default => 'secondary',
                })
                ->formatStateUsing(fn ($state) => ucfirst($state)),
            ])
            ->filters([
                SelectFilter::make('booking_type')
                    ->label('Booking Type')
                    ->options([
                        'tour' => 'Tour',
                        'hotel' => 'Hotel',
                        'taxi' => 'Taxi',
                        'restaurant' => 'Restaurant',
                        'package' => 'Package',
                    ]),

                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('booking_date', 'desc');
    }
    public static function canCreate(): bool
    {
        return false; 
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
