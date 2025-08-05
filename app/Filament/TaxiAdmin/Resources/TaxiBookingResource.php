<?php

namespace App\Filament\TaxiAdmin\Resources;

use App\Filament\TaxiAdmin\Resources\TaxiBookingResource\Pages;
use App\Filament\TaxiAdmin\Resources\TaxiBookingResource\RelationManagers;
use App\Models\TaxiBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxiBookingResource extends Resource
{
    protected static ?string $model = TaxiBooking::class;

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
            'index' => Pages\ListTaxiBookings::route('/'),
            'create' => Pages\CreateTaxiBooking::route('/create'),
            'edit' => Pages\EditTaxiBooking::route('/{record}/edit'),
        ];
    }
}
