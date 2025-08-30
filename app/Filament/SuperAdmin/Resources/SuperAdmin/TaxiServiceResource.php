<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin;

use App\Filament\SuperAdmin\Resources\SuperAdmin\TaxiServiceResource\Pages;
use App\Filament\SuperAdmin\Resources\SuperAdmin\TaxiServiceResource\RelationManagers;
use App\Models\TaxiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxiServiceResource extends Resource
{
    protected static ?string $model = TaxiService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->nullable(),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required()
                    ->label('Location'),
                Forms\Components\FileUpload::make('logo_url')
                    ->image()
                    ->directory('taxi-services')
                    ->nullable(),
                Forms\Components\TextInput::make('website')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\Toggle::make('is_active')
                    ->nullable(),
                Forms\Components\Select::make('manager_id')
                    ->relationship(
                        'manager',
                        'name',
                        fn(Builder $query) => $query->where('role', 'admin')->where('section', 'taxi')
                    )
                    ->nullable()
                    ->label('Manager'),
                Forms\Components\TimePicker::make('open_time')
                    ->nullable(),
                Forms\Components\TimePicker::make('close_time')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('logo_url')
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('manager.email')
                    ->label('Manager Name')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListTaxiServices::route('/'),
            'create' => Pages\CreateTaxiService::route('/create'),
            'edit' => Pages\EditTaxiService::route('/{record}/edit'),
        ];
    }
}
