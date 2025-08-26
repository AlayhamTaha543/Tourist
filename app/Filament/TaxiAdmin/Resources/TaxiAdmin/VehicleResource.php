<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleResource\Pages;
use App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleResource\RelationManagers;
use App\Models\TaxiService;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Filament\Facades\Filament;
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

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

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
                Forms\Components\Section::make('Vehicle Details')
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
                        Select::make('vehicle_type_id')
                            ->label('Vehicle Type')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return VehicleType::where('taxi_service_id', $admin->taxi()->first()->id)->pluck('name', 'id');
                                }
                                return VehicleType::pluck('name', 'id');
                            })
                            ->required(),
                        TextInput::make('registration_number')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('model')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('year')
                            ->numeric()
                            ->required(),
                        TextInput::make('color')
                            ->maxLength(255),
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
                TextColumn::make('vehicleType.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('registration_number')
                    ->searchable(),
                TextColumn::make('model')
                    ->searchable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('color')
                    ->searchable(),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
