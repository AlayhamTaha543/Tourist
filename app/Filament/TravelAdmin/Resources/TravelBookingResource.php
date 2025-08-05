<?php

namespace App\Filament\TravelAdmin\Resources;

use App\Filament\TravelAdmin\Resources\TravelBookingResource\Pages;
use App\Filament\TravelAdmin\Resources\TravelBookingResource\RelationManagers;
use App\Models\TravelBooking;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TravelBookingResource extends Resource
{
    protected static ?string $model = TravelBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Travel Managment';

    public static function canAccess(): bool
    {
        return Filament::auth()->check()  
        &&((Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'travel')
        ||(Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'travel'));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
        &&((Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'travel')
        ||(Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'travel'));
    }
    public static function getEloquentQuery(): Builder
    {
        if (Filament::auth()->user()->role === 'admin') {
            return parent::getEloquentQuery();
        }
        return parent::getEloquentQuery()
        ->whereHas('flight.agency', function ($query) {
            $query->where('admin_id', auth()->id());
        });
    }
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
                Tables\Columns\TextColumn::make('user.first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('flight.flight_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_people')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking.status')
                    ->label('Status')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('booking.payment_status')
                    ->label('payment status')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('booking.discount_amount')
                    ->label('discount')
                        ->numeric()
                        ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function canCreate(): bool
    {
        return false; 
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravelBookings::route('/'),
            'create' => Pages\CreateTravelBooking::route('/create'),
            'view' => Pages\ViewTravelBooking::route('/{record}'),
            'edit' => Pages\EditTravelBooking::route('/{record}/edit'),
        ];
    }
}
