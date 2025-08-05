<?php

namespace App\Filament\TravelAdmin\Resources;

use App\Filament\TravelAdmin\Resources\TravelAgencyResource\Pages;
use App\Filament\TravelAdmin\Resources\TravelAgencyResource\RelationManagers;
use App\Models\Admin;
use App\Models\TravelAgency;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class TravelAgencyResource extends Resource
{
    protected static ?string $model = TravelAgency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Travel Managment';

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && (Filament::auth()->user()->role === 'super_admin' 
         
         || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'travel'
         ));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()   
        && (Filament::auth()->user()->role === 'super_admin'  
        || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'travel'
         ));
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    
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
                    Forms\Components\Select::make('admin_id')
                        ->label('Manager')
                        ->options(function () {
                            $section = auth()->user()?->section;
                            return Admin::where('role', 'sub_admin')
                                ->where('section', $section)
                                ->pluck('name', 'id')
                                ->toArray();
                        })->required()
                        ->rule(function () {
                            return Rule::unique('restaurants', 'admin_id');
                        }),
                        Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
                ])
                ->columns(3),
        
            Forms\Components\Section::make('Contact & Media')
                ->schema([
                    Forms\Components\TextInput::make('logo')
                        ->label('Logo URL')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('website')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->maxLength(255),
                ])
                ->columns(2),
        
            Forms\Components\Section::make('Ratings & Status')
                ->schema([
                    Forms\Components\TextInput::make('average_rating')
                        ->label('Average Rating')
                        ->required()
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('total_ratings')
                        ->label('Total Ratings')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->required(),
                ])
                ->columns(3),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_ratings')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('logo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('admin_id')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListTravelAgencies::route('/'),
            'create' => Pages\CreateTravelAgency::route('/create'),
            'view' => Pages\ViewTravelAgency::route('/{record}'),
            'edit' => Pages\EditTravelAgency::route('/{record}/edit'),
        ];
    }
}
