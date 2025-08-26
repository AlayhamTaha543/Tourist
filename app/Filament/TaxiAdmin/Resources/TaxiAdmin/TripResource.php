<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TripResource\Pages;
use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TripResource\RelationManagers;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Taxi Management';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $admin = Filament::auth()->user();

        if ($admin && $admin->taxi()->first()) {
            $taxiServiceId = $admin->taxi()->first()->id;
            $query->whereHas('driver', function (Builder $driverQuery) use ($taxiServiceId) {
                $driverQuery->where('taxi_service_id', $taxiServiceId);
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Trip Details')
                    ->schema([
                        Select::make('driver_id')
                            ->label('Driver')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return Driver::where('taxi_service_id', $admin->taxi()->first()->id)->pluck('admin.name', 'id');
                                }
                                return Driver::pluck('admin.name', 'id');
                            }),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required(),
                        TextInput::make('status')
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('requested_at'),
                        DateTimePicker::make('started_at'),
                        DateTimePicker::make('completed_at'),
                        TextInput::make('fare')
                            ->numeric(),
                        TextInput::make('distance_km')
                            ->numeric(),
                        TextInput::make('surge_multiplier')
                            ->numeric(),
                        TextInput::make('trip_type')
                            ->maxLength(255),
                        Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return Vehicle::where('taxi_service_id', $admin->taxi()->first()->id)->pluck('registration_number', 'id');
                                }
                                return Vehicle::pluck('registration_number', 'id');
                            }),
                        TextInput::make('pickup_location')
                            ->maxLength(255),
                        TextInput::make('dropoff_location')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('driver.admin.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('fare')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('distance_km')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('surge_multiplier')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('trip_type')
                    ->searchable(),
                TextColumn::make('vehicle.registration_number')
                    ->searchable()
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
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'edit' => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}