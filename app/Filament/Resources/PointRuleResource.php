<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointRuleResource\Pages;
use App\Filament\Resources\PointRuleResource\RelationManagers;
use App\Models\PointRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PointRuleResource extends Resource
{
    protected static ?string $model = PointRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationGroup = 'Point Managment';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('action')
                    ->required()
                    ->options([
                        'book_flight' => 'book_flight',
                        'book_tour' => ' book_tour',
                        'book_hotel' => 'book_hotel ',
                        'book_restaurant' => 'book_restaurant ',
                        'add_restaurant_order' => 'add_restaurant_order ',
                    ])
                    ->label('Booking Type '),
                Forms\Components\TextInput::make('points')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action')
                ->searchable(),
                Tables\Columns\TextColumn::make('points')
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPointRules::route('/'),
            'create' => Pages\CreatePointRule::route('/create'),
            'view' => Pages\ViewPointRule::route('/{record}'),
            'edit' => Pages\EditPointRule::route('/{record}/edit'),
        ];
    }
}
