<?php

namespace App\Filament\TravelAdmin\Resources\TravelAdmin;

use App\Filament\TravelAdmin\Resources\TravelAdmin\TravelBookingResource\Pages;
use App\Filament\TravelAdmin\Resources\TravelAdmin\TravelBookingResource\RelationManagers;
use App\Models\TravelAdmin\TravelBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TravelBookingResource extends Resource
{
    protected static ?string $model = TravelBooking::class;

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
            'index' => Pages\ListTravelBookings::route('/'),
            'create' => Pages\CreateTravelBooking::route('/create'),
            'edit' => Pages\EditTravelBooking::route('/{record}/edit'),
        ];
    }
}
