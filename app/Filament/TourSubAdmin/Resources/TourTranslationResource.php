<?php

namespace App\Filament\TourSubAdmin\Resources;

use App\Filament\TourSubAdmin\Resources\TourTranslationResource\Pages;
use App\Filament\TourSubAdmin\Resources\TourTranslationResource\RelationManagers;
use App\Models\TourTranslation;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TourTranslationResource extends Resource
{
    protected static ?string $model = TourTranslation::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Tour Managment';

    protected static ?int $navigationSort = 5;
    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && Filament::auth()->user()->role === 'sub_admin' 
         && Filament::auth()->user()->section === 'tour'
         ;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
        && Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'tour';
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->whereHas('tour', function ($query) {
            $query->where('admin_id', auth()->id());
        });
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('tour_id')
                ->default(fn () => \App\Models\Tour::where('admin_id', auth()->id())->value('id')),
                
                Forms\Components\TextInput::make('language_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('translated_description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tour.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('translated_description'),
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
            'index' => Pages\ListTourTranslations::route('/'),
            'create' => Pages\CreateTourTranslation::route('/create'),
            'view' => Pages\ViewTourTranslation::route('/{record}'),
            'edit' => Pages\EditTourTranslation::route('/{record}/edit'),
        ];
    }
}
