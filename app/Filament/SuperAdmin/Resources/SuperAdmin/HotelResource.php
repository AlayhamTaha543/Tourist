<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin;

use App\Filament\SuperAdmin\Resources\SuperAdmin\HotelResource\Pages;
use App\Filament\SuperAdmin\Resources\SuperAdmin\HotelResource\RelationManagers;
use App\Models\Hotel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('admin_id')
                    ->relationship('admin', 'email', fn (Builder $query) => $query->where('role', 'hotel_admin'))
                    ->required()
                    ->label('Hotel Admin Email'),
                Forms\Components\TextInput::make('name')->required()->live(onBlur: true),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('phone')->tel(),
                Forms\Components\TextInput::make('website')->url(),
                Forms\Components\Toggle::make('is_recommended'),
                Forms\Components\Toggle::make('is_popular'),
                Forms\Components\Toggle::make('is_active'),
                Forms\Components\Toggle::make('is_featured'),
                Forms\Components\TimePicker::make('checkOut_time')
                    ->displayFormat('H:i')
                    ->format('H:i:s')
                    ->nullable(),
                Forms\Components\TimePicker::make('checkIn_time')
                    ->displayFormat('H:i')
                    ->format('H:i:s')
                    ->nullable(),
                Forms\Components\TextInput::make('discount')->numeric(),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('latitude')->numeric()->required(),
                        Forms\Components\TextInput::make('longitude')->numeric()->required(),
                        Forms\Components\Select::make('city_id')
                            ->relationship('city', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('region'),
                        Forms\Components\Toggle::make('is_popular'),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return \App\Models\Location::create($data);
                    })
                    ->required(),
                Forms\Components\FileUpload::make('main_image')
                    ->image()
                    ->directory('images/hotels')
                    ->getUploadedFileNameForStorageUsing(function ($file, $get) {
                        return $get('name') . '-' . time() . '.png';
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('location.name')->searchable(),
                Tables\Columns\TextColumn::make('ratings_average')->label('Average Rating'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
