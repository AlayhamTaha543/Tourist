<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleTypeResource\Pages;
use App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleTypeResource\RelationManagers;
use App\Models\TaxiService;
use App\Models\VehicleType;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Taxi Management';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $admin = Filament::auth()->user();

        if ($admin && $admin->taxi()->first()) {
            $query->where('taxi_service_id', $admin->taxi()->first()->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vehicle Type Details')
                    ->schema([
                        Select::make('taxi_service_id')
                            ->label('Taxi Service')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return TaxiService::where('id', $admin->taxi()->first()->id)->pluck('name', 'id');
                                }
                                return TaxiService::pluck('name', 'id');
                            })
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('description')
                            ->maxLength(255),
                        TextInput::make('max_passengers')
                            ->numeric()
                            ->required(),
                        TextInput::make('price_per_km')
                            ->numeric()
                            ->required(),
                        TextInput::make('base_price')
                            ->numeric()
                            ->required(),
                        FileUpload::make('image_url')
                            ->image()
                            ->directory('vehicle-types')
                            ->nullable(),
                        Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('taxiService.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('max_passengers')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_per_km')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('base_price')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image_url')
                    ->circular(),
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
            'index' => Pages\ListVehicleTypes::route('/'),
            'create' => Pages\CreateVehicleType::route('/create'),
            'edit' => Pages\EditVehicleType::route('/{record}/edit'),
        ];
    }
}