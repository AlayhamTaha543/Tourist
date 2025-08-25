<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin;

use App\Filament\SuperAdmin\Resources\SuperAdmin\LocationResource\Pages\EditLocation;
use App\Filament\SuperAdmin\Resources\SuperAdmin\LocationResource\Pages\CreateLocation;
use App\Filament\SuperAdmin\Resources\SuperAdmin\LocationResource\Pages\ListLocations;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\City;
use App\Models\Country;
use App\Models\Location;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Location Managment';

    public static function canAccess(): bool
    {
        return Filament::auth()->check()
            && Filament::auth()->user()->role === 'super_admin';
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()
            && Filament::auth()->user()->role === 'super_admin';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        Forms\Components\Toggle::make('is_popular')
                            ->label('Is Popular')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('city_id')
                                    ->label('City')
                                    ->options(City::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Select::make('country_id')
                                            ->label('Country')
                                            ->options(Country::all()->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Forms\Components\TextInput::make('description')
                                            ->maxLength(255),
                                        Forms\Components\Toggle::make('is_popular')
                                            ->label('Is Popular')
                                            ->default(false),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        // The model's cast handles boolean conversion, no explicit cast needed here.
                                        $city = City::create($data);
                                        return $city->id;
                                    }),

                                Forms\Components\Select::make('country_id')
                                    ->label('Country')
                                    ->options(Country::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('language')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('description')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('currency')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('code')
                                            ->maxLength(255),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $country = Country::create($data);
                                        return $country->id;
                                    }),

                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->required()
                                    ->readonly(),

                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->required()
                                    ->readonly(),

                                Forms\Components\TextInput::make('region')
                                    ->label('Region'),
                            ]),

                        Forms\Components\Placeholder::make('Map')
                            ->label('Map')
                            ->content(fn() => view('map'))
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.country.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_popular')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'edit' => EditLocation::route('/{record}/edit'),
        ];
    }
}
