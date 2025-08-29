<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin;

use App\Filament\SuperAdmin\Resources\SuperAdmin\RentalOfficeResource\Pages;
use App\Filament\SuperAdmin\Resources\SuperAdmin\RentalOfficeResource\RelationManagers;
use App\Models\RentalOffice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentalOfficeResource extends Resource
{
    protected static ?string $model = RentalOffice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->nullable(),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required()
                    ->label('Location'),
                Forms\Components\Select::make('manager_id')
                    ->relationship('manager', 'name')
                    ->required()
                    ->label('Manager'),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('rental-offices')
                    ->nullable(),
                Forms\Components\TimePicker::make('open_time')
                    ->nullable(),
                Forms\Components\TimePicker::make('close_time')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('manager.name')
                    ->label('Manager Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image'),
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
            'index' => Pages\ListRentalOffices::route('/'),
            'create' => Pages\CreateRentalOffice::route('/create'),
            'edit' => Pages\EditRentalOffice::route('/{record}/edit'),
        ];
    }
}
