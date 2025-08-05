<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserRankResource\Pages;
use App\Filament\Resources\UserRankResource\RelationManagers;
use App\Models\UserRank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserRankResource extends Resource
{
    protected static ?string $model = UserRank::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Point Managment';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.first_name')
                ->searchable(),
                Tables\Columns\TextColumn::make('rank.name')
                ->searchable(),
                Tables\Columns\TextColumn::make('points_earned')
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
    public static function canCreate(): bool
    {
        return false; 
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
            'index' => Pages\ListUserRanks::route('/'),
            'create' => Pages\CreateUserRank::route('/create'),
            'view' => Pages\ViewUserRank::route('/{record}'),
            'edit' => Pages\EditUserRank::route('/{record}/edit'),
        ];
    }
}
