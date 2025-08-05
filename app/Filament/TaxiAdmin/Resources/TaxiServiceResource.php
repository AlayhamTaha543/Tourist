<?php

namespace App\Filament\TaxiAdmin\Resources;

use App\Filament\TaxiAdmin\Resources\TaxiServiceResource\Pages;
use App\Filament\TaxiAdmin\Resources\TaxiServiceResource\RelationManagers;
use App\Models\TaxiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxiServiceResource extends Resource
{
    protected static ?string $model = TaxiService::class;
    protected static ?string $navigationGroup = 'Taxi Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Taxi Services';

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
            'index' => Pages\ListTaxiServices::route('/'),
            'create' => Pages\CreateTaxiService::route('/create'),
            'edit' => Pages\EditTaxiService::route('/{record}/edit'),
        ];
    }
}