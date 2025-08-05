<?php

namespace App\Filament\HotelAdmin\Resources;

use App\Filament\HotelAdmin\Resources\HotelResource\Pages;
use App\Filament\Resources\HotelResource\RelationManagers;
use App\Models\Admin;
use App\Models\Hotel;
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

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Hotel Managment';

    protected static ?int $navigationSort = 1;
    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
        && (Filament::auth()->user()->role === 'super_admin' 
         
         || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'hotel'
         ));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()   
        && (Filament::auth()->user()->role === 'super_admin'  
        || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'hotel'
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
                            ->maxLength(255),
                        FileUpload::make('main_image')
                                    ->label('Hotel Image')
                                    ->image()
                                    ->directory('hotel_images') 
                                    ->visibility('public' && 'storage'),
                        Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),            
                    ])->columns(3),
                Forms\Components\Section::make('INFO')
                    ->schema([
                    
                    Forms\Components\TextInput::make('star_rating')
                        ->numeric()
                        ->default(null),
                    Forms\Components\TextInput::make('average_rating')
                        ->required()
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('total_ratings')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Forms\Components\TimePicker::make('check_in_time'),
                    Forms\Components\TimePicker::make('check_out_time'),    
                    ])->columns(3),
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
                Forms\Components\Section::make('INFO')
                    ->schema([
                    Forms\Components\TextInput::make('website')
                        ->maxLength(255)
                        ->default(null),
                    Forms\Components\TextInput::make('phone')
                        ->maxLength(255)
                        ->default(null),
                    Forms\Components\TextInput::make('email')
                        ->maxLength(255)
                        ->default(null),
                    ])->columns(3),
                    Forms\Components\Section::make('INFO')
                    ->schema([
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
                    ->required()->rule(function () {
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
                ImageColumn::make('main_image')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => asset(asset('images/'.$record->mainImageURL) )) 
                    ->width(50),  
                Tables\Columns\TextColumn::make('admin.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checkIn_time'),
                Tables\Columns\TextColumn::make('checkOut_time'),
                Tables\Columns\TextColumn::make('star_rating')
                        ->numeric()
                        ->sortable(),
                Tables\Columns\TextColumn::make('average_rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_ratings')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('website')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'view' => Pages\ViewHotel::route('/{record}'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
