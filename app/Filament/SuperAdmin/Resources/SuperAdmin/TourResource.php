<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin;

use App\Filament\SuperAdmin\Resources\SuperAdmin\TourResource\Pages;
use App\Filament\SuperAdmin\Resources\SuperAdmin\TourResource\RelationManagers;
use App\Models\Tour;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TourResource extends Resource
{
    protected static ?string $model = Tour::class;

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
                Forms\Components\TextInput::make('short_description')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'name')
                    ->nullable()
                    ->label('Location'),
                Forms\Components\TextInput::make('duration_hours')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('duration_days')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('base_price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('language')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('discount_percentage')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('max_capacity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('min_participants')
                    ->numeric()
                    ->nullable(),
                Forms\Components\Select::make('difficulty_level')
                    ->options([
                        'easy' => 'Easy',
                        'moderate' => 'Moderate',
                        'difficult' => 'Difficult',
                    ])
                    ->nullable(),
                Forms\Components\TextInput::make('average_rating')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('total_ratings')
                    ->numeric()
                    ->nullable(),
                Forms\Components\FileUpload::make('main_image')
                    ->image()
                    ->directory('tours')
                    ->nullable(),
                Forms\Components\Toggle::make('is_active')
                    ->nullable(),
                Forms\Components\Toggle::make('is_featured')
                    ->nullable(),

                Forms\Components\Select::make('admin_id')
                    ->relationship(
                        'admin',
                        'name',
                        fn(Builder $query) => $query->where('role', 'admin')->where('section', 'tour')
                    )
                    ->nullable()
                    ->label('Manager'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Admin Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->money('USD')
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
            'index' => Pages\ListTours::route('/'),
            'create' => Pages\CreateTour::route('/create'),
            'edit' => Pages\EditTour::route('/{record}/edit'),
        ];
    }
}
