<?php

namespace App\Filament\RestaurantAdmin\Resources;

use App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource\Pages;
use App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource\RelationManagers;
use App\Models\RestaurantBooking;
use App\Models\Booking;
use App\Models\Restaurant;
use App\Models\RestaurantChair;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RestaurantBookingResource extends Resource
{
    protected static ?string $model = RestaurantBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('booking_id')
                    ->label('Booking')
                    ->options(Booking::all()->pluck('id', 'id')) // Assuming 'id' is a suitable display for Booking
                    ->required()
                    ->searchable(),
                Select::make('user_id')
                    ->label('User')
                    ->options(User::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('restaurant_id')
                    ->label('Restaurant')
                    ->options(Restaurant::all()->pluck('first_name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('restaurant_chair_id')
                    ->label('Restaurant Chair')
                    ->options(RestaurantChair::all()->pluck('location', 'id'))
                    ->required()
                    ->searchable(),
                DatePicker::make('reservation_date')
                    ->required(),
                TimePicker::make('reservation_time')
                    ->required(),
                TextInput::make('number_of_guests')
                    ->required()
                    ->numeric(),
                TextInput::make('cost')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('duration_time')
                    ->required()
                    ->numeric()
                    ->suffix('hours'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_id')
                    ->label('Booking ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.first_name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('restaurant.name')
                    ->label('Restaurant Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('restaurantChair.location')
                    ->label('Chair Location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reservation_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('reservation_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('number_of_guests')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('duration_time')
                    ->numeric()
                    ->suffix(' hours')
                    ->sortable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestaurantBookings::route('/'),
            'create' => Pages\CreateRestaurantBooking::route('/create'),
            'edit' => Pages\EditRestaurantBooking::route('/{record}/edit'),
        ];
    }
}
