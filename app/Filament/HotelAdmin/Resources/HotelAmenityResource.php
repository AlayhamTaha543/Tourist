<?php

namespace App\Filament\HotelAdmin\Resources;

use App\Filament\HotelAdmin\Resources\HotelAmenityResource\Pages;
use App\Filament\Resources\HotelAmenityResource\RelationManagers;
use App\Models\HotelAmenity;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelAmenityResource extends Resource
{
    protected static ?string $model = HotelAmenity::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Hotel Managment';

    protected static ?int $navigationSort = 4;
    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && Filament::auth()->user()->role === 'admin' 
         && Filament::auth()->user()->section === 'hotel'
         ;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
         && Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'hotel';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('icon')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->label('Icon')
                    ->getStateUsing(fn ($record) => "<i class='{$record->icon}' style='font-size: 20px'></i>")
                    ->html(),
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
            'index' => Pages\ListHotelAmenities::route('/'),
            'create' => Pages\CreateHotelAmenity::route('/create'),
            'view' => Pages\ViewHotelAmenity::route('/{record}'),
            'edit' => Pages\EditHotelAmenity::route('/{record}/edit'),
        ];
    }
}
