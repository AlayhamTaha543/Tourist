<?php

namespace App\Filament\RestaurantAdmin\Resources;

use App\Filament\RestaurantAdmin\Resources\RestaurantChairResource\Pages;
use App\Filament\RestaurantAdmin\Resources\RestaurantChairResource\RelationManagers;
use App\Models\RestaurantChair;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RestaurantChairResource extends Resource
{
    protected static ?string $model = RestaurantChair::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('restaurant_id')
                    ->label('Restaurant')
                    ->options(Restaurant::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_reservable')
                    ->label('Is Reservable')
                    ->inline(false),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->inline(false),
                TextInput::make('cost')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('total_chairs')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('restaurant.name')
                    ->label('Restaurant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_reservable')
                    ->label('Reservable')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('total_chairs')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRestaurantChairs::route('/'),
            'create' => Pages\CreateRestaurantChair::route('/create'),
            'edit' => Pages\EditRestaurantChair::route('/{record}/edit'),
        ];
    }
}