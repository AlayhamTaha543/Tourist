<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TaxiBookingResource\Pages;
use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TaxiBookingResource\RelationManagers;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Location;
use App\Models\TaxiBooking;
use App\Models\TaxiService;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxiBookingResource extends Resource
{
    protected static ?string $model = TaxiBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Taxi Management';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $admin = Filament::auth()->user();

        if ($admin && $admin->taxi()->first()) {
            $query->where('taxi_service_id', $admin->taxi()->first()->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Details')
                    ->schema([
                        Select::make('booking_id')
                            ->relationship('booking', 'id')
                            ->required(),
                        Select::make('taxi_service_id')
                            ->label('Taxi Service')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return TaxiService::where('id', $admin->taxi()->first()->id)->pluck('name', 'id');
                                }
                                return TaxiService::pluck('name', 'id');
                            })
                            ->required(),
                        Select::make('vehicle_type_id')
                            ->label('Vehicle Type')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return VehicleType::where('taxi_service_id', $admin->taxi()->first()->id)->pluck('name', 'id');
                                }
                                return VehicleType::pluck('name', 'id');
                            })
                            ->required(),
                        Select::make('trip_id')
                            ->label('Trip')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return Trip::whereHas('driver', function (Builder $query) use ($admin) {
                                        $query->where('taxi_service_id', $admin->taxi()->first()->id);
                                    })->pluck('id', 'id');
                                }
                                return Trip::pluck('id', 'id');
                            }),
                        Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return Vehicle::where('taxi_service_id', $admin->taxi()->first()->id)->pluck('registration_number', 'id');
                                }
                                return Vehicle::pluck('registration_number', 'id');
                            }),
                        Select::make('driver_id')
                            ->label('Driver')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return Driver::where('taxi_service_id', $admin->taxi()->first()->id)->pluck('admin.name', 'id');
                                }
                                return Driver::pluck('admin.name', 'id');
                            }),
                        Select::make('pickup_location_id')
                            ->relationship('pickupLocation', 'name')
                            ->required(),
                        Select::make('dropoff_location_id')
                            ->relationship('dropoffLocation', 'name')
                            ->required(),
                        DateTimePicker::make('pickup_date_time')
                            ->required(),
                        TextInput::make('type_of_booking')
                            ->maxLength(255),
                        TextInput::make('estimated_distance')
                            ->numeric(),
                        TextInput::make('duration_hours')
                            ->numeric(),
                        DateTimePicker::make('return_time'),
                        TextInput::make('status')
                            ->maxLength(255),
                        Toggle::make('is_scheduled'),
                        Toggle::make('is_shared'),
                        TextInput::make('passenger_count')
                            ->numeric(),
                        TextInput::make('max_additional_passengers')
                            ->numeric(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.id')
                    ->sortable(),
                TextColumn::make('taxiService.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicleType.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver.admin.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicle.registration_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pickupLocation.name')
                    ->searchable(),
                TextColumn::make('dropoffLocation.name')
                    ->searchable(),
                TextColumn::make('pickup_date_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                IconColumn::make('is_scheduled')
                    ->boolean(),
                IconColumn::make('is_shared')
                    ->boolean(),
                TextColumn::make('passenger_count')
                    ->numeric(),
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
            'index' => Pages\ListTaxiBookings::route('/'),
            'create' => Pages\CreateTaxiBooking::route('/create'),
            'edit' => Pages\EditTaxiBooking::route('/{record}/edit'),
        ];
    }
}