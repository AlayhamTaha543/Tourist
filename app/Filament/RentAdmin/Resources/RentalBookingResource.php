<?php

namespace App\Filament\RentAdmin\Resources;

use App\Filament\RentAdmin\Resources\RentalBookingResource\Pages;
use App\Filament\RentAdmin\Resources\RentalBookingResource\RelationManagers;
use App\Models\RentalBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentalBookingResource extends Resource
{
    protected static ?string $model = RentalBooking::class;

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
            'index' => Pages\ListRentalBookings::route('/'),
            'create' => Pages\CreateRentalBooking::route('/create'),
            'edit' => Pages\EditRentalBooking::route('/{record}/edit'),
        ];
    }
}
