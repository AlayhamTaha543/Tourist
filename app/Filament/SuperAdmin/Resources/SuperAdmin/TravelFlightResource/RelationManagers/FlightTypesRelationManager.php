<?php
namespace App\Filament\SuperAdmin\Resources\SuperAdmin\TravelFlightResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlightTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'flightTypes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('flight_type')
                    ->required()
                    ->maxLength(255)
                    ->label('Flight Type'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->label('Price'),
                Forms\Components\TextInput::make('available_seats')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->label('Available Seats'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('flight_type')
            ->columns([
                Tables\Columns\TextColumn::make('flight_type')
                    ->label('Flight Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_seats')
                    ->numeric()
                    ->sortable()
                    ->label('Available Seats'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
