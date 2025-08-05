<?php

namespace App\Filament\RentAdmin\Resources;

use App\Filament\RentAdmin\Resources\RentalVehicleResource\Pages;
use App\Filament\RentAdmin\Resources\RentalVehicleResource\RelationManagers;
use App\Models\RentalVehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentalVehicleResource extends Resource
{
    protected static ?string $model = RentalVehicle::class;

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
            'index' => Pages\ListRentalVehicles::route('/'),
            'create' => Pages\CreateRentalVehicle::route('/create'),
            'edit' => Pages\EditRentalVehicle::route('/{record}/edit'),
        ];
    }
}
