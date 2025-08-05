<?php

namespace App\Filament\TourAdmin\Resources;

use App\Filament\TourAdmin\Resources\TourResource\Pages;
use App\Filament\TourAdmin\Resources\TourResource\RelationManagers;
use App\Models\Admin;
use App\Models\Tour;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;


class TourResource extends Resource
{
    protected static ?string $model = Tour::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Tour Managment';

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
        && (Filament::auth()->user()->role === 'super_admin' 
        ||(
           Filament::auth()->user()->role === 'admin' 
           && Filament::auth()->user()->section === 'tour'
        ));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check() 
         && (Filament::auth()->user()->role === 'super_admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'tour'
         ));
    }

    public static function form(Form $form): Form
    {
        return $form
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
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('short_description')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('duration_hours')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('duration_days')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('base_price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('discount_percentage')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('max_capacity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('min_participants')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('difficulty_level')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('average_rating')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('total_ratings')
                    ->required()
                    ->numeric()
                    ->default(0),
                    Forms\Components\MultiSelect::make('categories')
                    ->relationship('categories', 'name')
                    ->label('Tour Categories')
                    ->preload(),
                FileUpload::make('main_image')
                ->label('Tour Image')
                ->image()
                ->directory('tour_images') 
                ->visibility('public'),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\Toggle::make('is_featured')
                    ->required(),
                    Forms\Components\Select::make('admin_id')
                    ->label('Manager')
                    ->options(function () {
                        $section = auth()->user()?->section;
                
                        return Admin::where('role', 'sub_admin')
                            ->where('section', $section)
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->rule(function () {
                        return Rule::unique('restaurants', 'admin_id');
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('short_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_hours')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_participants')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('difficultyLevel')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_ratings')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('main_image')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => asset('images/'.$record->main_image) ) 
                    ->height(50)
                    ->width(50),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_by')
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
            'index' => Pages\ListTours::route('/'),
            'create' => Pages\CreateTour::route('/create'),
            'view' => Pages\ViewTour::route('/{record}'),
            'edit' => Pages\EditTour::route('/{record}/edit'),
        ];
    }
}
