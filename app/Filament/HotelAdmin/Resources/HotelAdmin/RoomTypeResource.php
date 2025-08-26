<?php

namespace App\Filament\HotelAdmin\Resources\HotelAdmin;

use App\Filament\HotelAdmin\Resources\HotelAdmin\RoomTypeResource\Pages;
use App\Filament\HotelAdmin\Resources\HotelAdmin\RoomTypeResource\RelationManagers;
use App\Models\RoomType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomTypeResource extends Resource
{
    protected static ?string $model = RoomType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('hotel_id')
                    ->relationship('hotel', 'name')
                    ->required(),
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('number')->numeric()->required(),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
                TextInput::make('max_occupancy')->numeric()->required(),
                TextInput::make('base_price')->numeric()->required(),
                TextInput::make('discount_percentage')->numeric()->default(0),
                TextInput::make('size')->maxLength(255),
                TextInput::make('bed_type')->maxLength(255),
                FileUpload::make('image')->image()->directory('images/room-types'),
                Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('number')->sortable(),
                Tables\Columns\TextColumn::make('max_occupancy'),
                Tables\Columns\TextColumn::make('base_price')->money()->sortable(),
                Tables\Columns\TextColumn::make('discount_percentage')->suffix('%'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\ImageColumn::make('image'),
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
            'index' => Pages\ListRoomTypes::route('/'),
            'create' => Pages\CreateRoomType::route('/create'),
            'edit' => Pages\EditRoomType::route('/{record}/edit'),
        ];
    }
}
