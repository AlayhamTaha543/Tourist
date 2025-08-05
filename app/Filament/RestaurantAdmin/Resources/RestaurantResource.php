<?php

namespace App\Filament\RestaurantAdmin\Resources;

use App\Filament\RestaurantAdmin\Resources\RestaurantResource\Pages;
use App\Filament\RestaurantAdmin\Resources\RestaurantResource\RelationManagers;
use App\Models\Admin;
use App\Models\City;
use App\Models\Country;
use App\Models\Restaurant;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Log;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Restaurant Managment';
    
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && (Filament::auth()->user()->role === 'super_admin' 
         
         || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'restaurant'
         ));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()   
        && (Filament::auth()->user()->role === 'super_admin'  
        || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'restaurant'
         ));
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('INFO')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')->columnSpanFull(),
                    Forms\Components\TextInput::make('discount')->numeric()->default(null),
                    Forms\Components\TextInput::make('cost')->numeric()->default(null),
                    Forms\Components\TextInput::make('max_tables')->numeric()->default(null),
                    FileUpload::make('main_image')
                        ->label('Restaurant Image')
                        ->image()
                        ->directory('restaurant_images')
                        ->visibility('public'),
                ])->columns(2),

            Forms\Components\Section::make('Location')
                ->schema([
                    Forms\Components\TextInput::make('latitude')
                        ->label('Latitude')
                        ->required()
                        ->readonly(),
                    Forms\Components\TextInput::make('longitude')
                        ->label('Longitude')
                        ->required()
                        ->readonly(),
                        Forms\Components\TextInput::make('city')
                        ->label('City')
                        ->required(),
                    
                    Forms\Components\TextInput::make('country')
                        ->label('Country')
                        ->required(),
                    
                    Forms\Components\Placeholder::make('Map')
                        ->content(function () {
                            return view('map');
                        })->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Time')
                ->schema([
                    Forms\Components\TimePicker::make('opening_time')
                        ->native(false),
                    Forms\Components\TimePicker::make('closing_time')
                        ->native(false),
                ])->columns(2),

            Forms\Components\Section::make('Rating')
                ->schema([
                    Forms\Components\TextInput::make('average_rating')
                        ->required()
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('total_ratings')
                        ->required()
                        ->numeric()
                        ->default(0),
                ])->columns(2),

            Forms\Components\Section::make('Price Range')
                ->schema([
                    Forms\Components\Select::make('price_range')
                        ->options([
                            'inexpensive' => 'Inexpensive',
                            'moderate' => 'Moderate',
                            'expensive' => 'Expensive',
                            'very_expensive' => 'Very Expensive',
                        ])
                        ->label('Price Range'),
                ])->columns(1),

            Forms\Components\Section::make('Communication')
                ->schema([
                    Forms\Components\TextInput::make('website')->maxLength(255)->default(null),
                    Forms\Components\TextInput::make('phone')->maxLength(255)->default(null),
                    Forms\Components\TextInput::make('email')->maxLength(255)->default(null),
                ])->columns(3),

            Forms\Components\Section::make('Others')
                ->schema([
                    Forms\Components\Toggle::make('is_active')->required(),
                    Forms\Components\Toggle::make('is_featured')->required(),
                ])->columns(2),

            Forms\Components\Section::make('Users')
                ->schema([
                    Forms\Components\TextInput::make('cuisine')->maxLength(255)->default(null),
                    Forms\Components\Select::make('admin_id')
                        ->label('Manager')
                        ->options(function () {
                            $section = auth()->user()?->section;
                            return Admin::where('role', 'sub_admin')
                                ->where('section', $section)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->required()
                        ->rule(function () {
                            return Rule::unique('restaurants', 'admin_id');
                        }),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cuisine')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price_range')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('opening_time')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('closing_time')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('average_rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_ratings')
                    ->numeric()
                    ->sortable(),
                    ImageColumn::make('main_image')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => asset(asset('images/'.$record->MainImageURL) )) 
                    ->width(50),
                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('has_reservation')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
               
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
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'view' => Pages\ViewRestaurant::route('/{record}'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}
