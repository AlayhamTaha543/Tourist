<?php

namespace App\Filament\HotelSubAdmin\Resources;

use App\Filament\HotelSubAdmin\Resources\HotelAmenityMapResource\Pages;
use App\Filament\HotelSubAdmin\Resources\HotelAmenityMapResource\RelationManagers;
use App\Models\HotelAmenityMap;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelAmenityMapResource extends Resource
{
    protected static ?string $model = HotelAmenityMap::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Hotel Managment';

    protected static ?int $navigationSort = 2;
    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && Filament::auth()->user()->role === 'sub_admin' 
         && Filament::auth()->user()->section === 'hotel'
         ;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
        && Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'hotel';
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->whereHas('hotel', function ($query) {
            $query->where('admin_id', auth()->id());
        });
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Hidden::make('hotel_id')
                ->default(fn () => \App\Models\Hotel::where('admin_id', auth()->id())->value('id')),
                

            Select::make('amenity_id')
                ->label('Amenity')
                ->relationship('amenity', 'name')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amenity.name')
                    ->searchable(),
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
            'index' => Pages\ListHotelAmenityMaps::route('/'),
            'create' => Pages\CreateHotelAmenityMap::route('/create'),
            'view' => Pages\ViewHotelAmenityMap::route('/{record}'),
            'edit' => Pages\EditHotelAmenityMap::route('/{record}/edit'),
        ];
    }
}
