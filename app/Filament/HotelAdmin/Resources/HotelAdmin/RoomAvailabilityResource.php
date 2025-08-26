<?php

namespace App\Filament\HotelAdmin\Resources\HotelAdmin;

use App\Filament\HotelAdmin\Resources\HotelAdmin\RoomAvailabilityResource\Pages;
use App\Filament\HotelAdmin\Resources\HotelAdmin\RoomAvailabilityResource\RelationManagers;
use App\Models\RoomAvailability;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomAvailabilityResource extends Resource
{
    protected static ?string $model = RoomAvailability::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('room_type_id')
                    ->relationship('roomType', 'name')
                    ->required(),
                DatePicker::make('date')->required(),
                TextInput::make('available_rooms')->numeric()->required(),
                TextInput::make('price')->numeric()->required(),
                Toggle::make('is_blocked')->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roomType.name')->searchable(),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('available_rooms')->sortable(),
                Tables\Columns\TextColumn::make('price')->money()->sortable(),
                Tables\Columns\IconColumn::make('is_blocked')->boolean(),
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
            'index' => Pages\ListRoomAvailabilities::route('/'),
            'create' => Pages\CreateRoomAvailability::route('/create'),
            'edit' => Pages\EditRoomAvailability::route('/{record}/edit'),
        ];
    }
}
