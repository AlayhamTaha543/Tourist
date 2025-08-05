<?php

namespace App\Filament\RestaurantSubAdmin\Resources;

use App\Filament\RestaurantSubAdmin\Resources\RestaurantImageResource\Pages;
use App\Filament\Resources\RestaurantImageResource\RelationManagers;
use App\Models\RestaurantImage;
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

class RestaurantImageResource extends Resource
{
    protected static ?string $model = RestaurantImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $navigationGroup = 'Restaurant Managment';
    
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && Filament::auth()->user()->role === 'sub_admin' 
         && Filament::auth()->user()->section === 'restaurant'
         ;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
        && Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'restaurant';
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->whereHas('restaurant', function ($query) {
            $query->where('admin_id', auth()->id());
        });
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                ->label('Restaurant Image')
                ->image()
                ->directory('restaurant_images') 
                ->visibility('public')
                ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                    Forms\Components\Hidden::make('restaurant_id')
                    ->default(fn () => \App\Models\Restaurant::where('admin_id', auth()->id())->value('id')),
                Forms\Components\TextInput::make('displayOrder')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('caption')
                    ->maxLength(255)
                    ->default(null),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                ImageColumn::make('image')
                ->label('Image')
                ->getStateUsing(fn ($record) => asset('images/'.$record->imageURL) ) 
                ->height(50)
                ->width(50),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('restarant Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('display_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('caption')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListRestaurantImages::route('/'),
            'create' => Pages\CreateRestaurantImage::route('/create'),
            'view' => Pages\ViewRestaurantImage::route('/{record}'),
            'edit' => Pages\EditRestaurantImage::route('/{record}/edit'),
        ];
    }
}
