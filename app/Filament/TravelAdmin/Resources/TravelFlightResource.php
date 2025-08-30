<?php

namespace App\Filament\TravelAdmin\Resources\TravelAdmin;

use App\Filament\TravelAdmin\Resources\TravelAdmin\TravelFlightResource\Pages;
use App\Filament\TravelAdmin\Resources\TravelAdmin\TravelFlightResource\RelationManagers;
use App\Models\TravelAdmin\TravelFlight;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TravelFlightResource extends Resource
{
    protected static ?string $model = TravelFlight::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                //
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
            'index' => Pages\ListTravelFlights::route('/'),
            'create' => Pages\CreateTravelFlight::route('/create'),
            'edit' => Pages\EditTravelFlight::route('/{record}/edit'),
        ];
    }
}
