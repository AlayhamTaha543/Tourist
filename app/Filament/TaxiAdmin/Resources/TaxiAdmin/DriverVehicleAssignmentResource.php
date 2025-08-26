<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverVehicleAssignmentResource\Pages;
use App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverVehicleAssignmentResource\RelationManagers;
use App\Models\Driver;
use App\Models\DriverVehicleAssignment;
use App\Models\Vehicle;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DriverVehicleAssignmentResource extends Resource
{
    protected static ?string $model = DriverVehicleAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Taxi Management';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $admin = Filament::auth()->user();

        if ($admin && $admin->taxi()->first()) {
            $taxiServiceId = $admin->taxi()->first()->id;
            $query->whereHas('driver', function (Builder $driverQuery) use ($taxiServiceId) {
                $driverQuery->where('taxi_service_id', $taxiServiceId);
            })->orWhereHas('vehicle', function (Builder $vehicleQuery) use ($taxiServiceId) {
                $vehicleQuery->where('taxi_service_id', $taxiServiceId);
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Assignment Details')
                    ->schema([
                        Select::make('driver_id')
                            ->label('Driver')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return Driver::where('taxi_service_id', $admin->taxi()->first()->id)->pluck('admin.name', 'id');
                                }
                                return Driver::pluck('admin.name', 'id');
                            })
                            ->required(),
                        Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->options(function () {
                                $admin = Filament::auth()->user();
                                if ($admin && $admin->taxi()->first()) {
                                    return Vehicle::where('taxi_service_id', $admin->taxi()->first()->id)->pluck('registration_number', 'id');
                                }
                                return Vehicle::pluck('registration_number', 'id');
                            })
                            ->required(),
                        DateTimePicker::make('assigned_at')
                            ->required(),
                        DateTimePicker::make('unassigned_at'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('driver.admin.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicle.registration_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assigned_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('unassigned_at')
                    ->dateTime()
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
            'index' => Pages\ListDriverVehicleAssignments::route('/'),
            'create' => Pages\CreateDriverVehicleAssignment::route('/create'),
            'edit' => Pages\EditDriverVehicleAssignment::route('/{record}/edit'),
        ];
    }
}