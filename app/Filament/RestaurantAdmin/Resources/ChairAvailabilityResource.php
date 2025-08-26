<?php

namespace App\Filament\RestaurantAdmin\Resources;

use App\Filament\RestaurantAdmin\Resources\ChairAvailabilityResource\Pages;
use App\Filament\RestaurantAdmin\Resources\ChairAvailabilityResource\RelationManagers;
use App\Models\ChairAvailability;
use App\Models\RestaurantChair;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChairAvailabilityResource extends Resource
{
    protected static ?string $model = ChairAvailability::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('restaurant_chair_id')
                    ->label('Restaurant Chair')
                    ->options(RestaurantChair::all()->pluck('location', 'id'))
                    ->required()
                    ->searchable(),
                DatePicker::make('date')
                    ->required(),
                TimePicker::make('time_slot')
                    ->required(),
                TextInput::make('available_chairs_count')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('restaurantChair.location')
                    ->label('Restaurant Chair Location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('time_slot')
                    ->time()
                    ->sortable(),
                TextColumn::make('available_chairs_count')
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
            'index' => Pages\ListChairAvailabilities::route('/'),
            'create' => Pages\CreateChairAvailability::route('/create'),
            'edit' => Pages\EditChairAvailability::route('/{record}/edit'),
        ];
    }
}