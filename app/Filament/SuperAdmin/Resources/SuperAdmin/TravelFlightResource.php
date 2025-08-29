<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin;

use App\Filament\SuperAdmin\Resources\SuperAdmin\TravelFlightResource\Pages;
use App\Filament\SuperAdmin\Resources\SuperAdmin\TravelFlightResource\RelationManagers;
use App\Models\TravelFlight;
use Filament\Forms;
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
use App\Models\TravelAgency;
use App\Models\Location;

class TravelFlightResource extends Resource
{
    protected static ?string $model = TravelFlight::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Select::make('travel_agency_id')
                            ->label('Agency Name')
                            ->options(TravelAgency::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        TextInput::make('flight_number')
                            ->required()
                            ->maxLength(255),
                        Select::make('departure_location_id')
                            ->label('Departure Name')
                            ->options(Location::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Select::make('arrival_location_id')
                            ->label('Arrival Name')
                            ->options(Location::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        DateTimePicker::make('departure_time')
                            ->required(),
                        DateTimePicker::make('arrival_time')
                            ->required(),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('available_seats')
                            ->required()
                            ->numeric(),
                        Toggle::make('is_popular')
                            ->label('Is Popular')
                            ->inline(false),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('travelAgency.name')
                    ->label('Agency Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('departureLocation.name')
                    ->label('Departure Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('arrivalLocation.name')
                    ->label('Arrival Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('departure_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('arrival_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                IconColumn::make('is_popular')
                    ->label('Is Popular')
                    ->boolean(),
                TextColumn::make('available_seats')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            RelationManagers\FlightTypesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravelFlights::route('/'),
            'create' => Pages\CreateTravelFlight::route('/create'),
            'edit' => Pages\EditTravelFlight::route('/{record}/edit'),
        ];
    }
}
