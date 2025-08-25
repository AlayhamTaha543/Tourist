<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin;

use App\Filament\SuperAdmin\Resources\SuperAdmin\LocationResource\Pages;
use App\Filament\SuperAdmin\Resources\SuperAdmin\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Forms\Components\MapPickerField; // Import your custom field

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Select::make('city_id')
                    ->relationship('city', 'name')
                    ->required(),
                Forms\Components\TextInput::make('region')->maxLength(255),
                Forms\Components\Toggle::make('is_popular'),
                // Use your custom MapPickerField
                MapPickerField::make('location_picker') // This is a virtual field name for the component
                    ->label('Select Location on Map')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('latitude')
                    ->numeric()
                    ->required()
                    ->hidden(), // Keep hidden, updated by MapPickerField
                Forms\Components\TextInput::make('longitude')
                    ->numeric()
                    ->required()
                    ->hidden(), // Keep hidden, updated by MapPickerField
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('region')->searchable(),
                Tables\Columns\TextColumn::make('city.name')->label('City Name')->searchable(),
                Tables\Columns\TextColumn::make('city.country.name')->label('Country Name')->searchable(),
                Tables\Columns\IconColumn::make('is_popular')->boolean(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
