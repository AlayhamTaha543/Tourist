<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverResource\Pages;
use App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverResource\RelationManagers;
use App\Models\Admin;
use App\Models\Driver;
use App\Models\TaxiService;
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

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Taxi Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Driver Details')
                    ->schema([
                        Select::make('admin_id')
                            ->relationship('admin', 'name')
                            ->required(),
                        Select::make('taxi_service_id')
                            ->relationship('taxiService', 'name')
                            ->required(),
                        TextInput::make('license_number')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('experience_years')
                            ->numeric()
                            ->required(),
                        TextInput::make('rating')
                            ->numeric()
                            ->default(0.0),
                        Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admin.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('taxiService.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('license_number')
                    ->searchable(),
                TextColumn::make('experience_years')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
