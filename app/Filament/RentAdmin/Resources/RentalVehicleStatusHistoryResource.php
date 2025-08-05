<?php

namespace App\Filament\RentAdmin\Resources;

use App\Filament\RentAdmin\Resources\RentalVehicleStatusHistoryResource\Pages;
use App\Filament\RentAdmin\Resources\RentalVehicleStatusHistoryResource\RelationManagers;
use App\Models\RentalVehicleStatusHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentalVehicleStatusHistoryResource extends Resource
{
    protected static ?string $model = RentalVehicleStatusHistory::class;

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
            'index' => Pages\ListRentalVehicleStatusHistories::route('/'),
            'create' => Pages\CreateRentalVehicleStatusHistory::route('/create'),
            'edit' => Pages\EditRentalVehicleStatusHistory::route('/{record}/edit'),
        ];
    }
}
