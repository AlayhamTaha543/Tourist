<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin;

use App\Filament\SuperAdmin\Resources\SuperAdmin\RestaurantResource\Pages;
use App\Filament\SuperAdmin\Resources\SuperAdmin\RestaurantResource\RelationManagers;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->nullable(),
                Forms\Components\TextInput::make('discount')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('latitude')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('longitude')
                    ->numeric()
                    ->nullable(),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'name')
                    ->nullable()
                    ->label('Location'),
                Forms\Components\TextInput::make('cuisine')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\Select::make('price_range')
                    ->options([
                        'inexpensive' => 'Inexpensive',
                        'moderate' => 'Moderate',
                        'expensive' => 'Expensive',
                        'very_expensive' => 'Very Expensive',
                    ])
                    ->nullable(),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TimePicker::make('opening_time')
                    ->nullable(),
                Forms\Components\TimePicker::make('closing_time')
                    ->nullable(),
                Forms\Components\TextInput::make('average_rating')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('total_ratings')
                    ->numeric()
                    ->nullable(),
                Forms\Components\FileUpload::make('main_image')
                    ->image()
                    ->directory('restaurants')
                    ->nullable(),
                Forms\Components\TextInput::make('website')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('max_chairs')
                    ->numeric()
                    ->nullable(),
                Forms\Components\Toggle::make('has_reservation')
                    ->nullable(),
                Forms\Components\Toggle::make('is_active')
                    ->nullable(),
                Forms\Components\Toggle::make('is_featured')
                    ->nullable(),
                Forms\Components\Toggle::make('is_popular')
                    ->nullable(),
                Forms\Components\Toggle::make('is_recommended')
                    ->nullable(),
                Forms\Components\Select::make('admin_id')
                    ->relationship('admin', 'name')
                    ->required()
                    ->label('Admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Admin Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_recommended')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_popular')
                    ->boolean(),
                Tables\Columns\TextColumn::make('max_chairs')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}
