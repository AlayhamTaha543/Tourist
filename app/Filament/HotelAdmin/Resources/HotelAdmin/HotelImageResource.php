<?php

namespace App\Filament\HotelAdmin\Resources\HotelAdmin;

use App\Filament\HotelAdmin\Resources\HotelAdmin\HotelImageResource\Pages;
use App\Filament\HotelAdmin\Resources\HotelAdmin\HotelImageResource\RelationManagers;
use App\Models\HotelAdmin\HotelImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelImageResource extends Resource
{
    protected static ?string $model = HotelImage::class;

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
            'index' => Pages\ListHotelImages::route('/'),
            'create' => Pages\CreateHotelImage::route('/create'),
            'edit' => Pages\EditHotelImage::route('/{record}/edit'),
        ];
    }
}
