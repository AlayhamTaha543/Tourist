<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountPointResource\Pages;
use App\Filament\Resources\DiscountPointResource\RelationManagers;
use App\Models\DiscountPoint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscountPointResource extends Resource
{
    protected static ?string $model = DiscountPoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    
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
                    ->label('Booking Type ')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('required_points')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('discount_percentage')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('required_points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListDiscountPoints::route('/'),
            'create' => Pages\CreateDiscountPoint::route('/create'),
            'view' => Pages\ViewDiscountPoint::route('/{record}'),
            'edit' => Pages\EditDiscountPoint::route('/{record}/edit'),
        ];
    }
}
