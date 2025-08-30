<?php

namespace App\Filament\TravelAdmin\Resources\TravelAdmin;

use App\Filament\TravelAdmin\Resources\TravelAdmin\FlightTypeResource\Pages;
use App\Filament\TravelAdmin\Resources\TravelAdmin\FlightTypeResource\RelationManagers;
use App\Models\TravelAdmin\FlightType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlightTypeResource extends Resource
{
    protected static ?string $model = FlightType::class;

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
            'index' => Pages\ListFlightTypes::route('/'),
            'create' => Pages\CreateFlightType::route('/create'),
            'edit' => Pages\EditFlightType::route('/{record}/edit'),
        ];
    }
}
