<?php

namespace App\Filament\HotelSubAdmin\Resources;

use App\Filament\HotelSubAdmin\Resources\HotelImageResource\Pages;
use App\Filament\Resources\HotelImageResource\RelationManagers;
use App\Models\HotelImage;
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

class HotelImageResource extends Resource
{
    protected static ?string $model = HotelImage::class;
    protected static ?string $navigationIcon = 'heroicon-o-camera';
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
                
                Forms\Components\TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('caption')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                FileUpload::make('image')
                    ->label('Hotel Image')
                    ->image()
                    ->directory('hotel_images') 
                    ->visibility('public')
                    ->required(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')
                    ->numeric()
                    ->sortable(),
                    ImageColumn::make('image')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => asset(asset('images/'.$record->image) )) 
                    ->width(50),
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
            'index' => Pages\ListHotelImages::route('/'),
            'create' => Pages\CreateHotelImage::route('/create'),
            'view' => Pages\ViewHotelImage::route('/{record}'),
            'edit' => Pages\EditHotelImage::route('/{record}/edit'),
        ];
    }
}
