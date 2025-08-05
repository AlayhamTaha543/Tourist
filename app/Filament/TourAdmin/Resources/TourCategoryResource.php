<?php

namespace App\Filament\TourAdmin\Resources;

use App\Filament\TourAdmin\Resources\TourCategoryResource\Pages;
use App\Filament\TourAdmin\Resources\TourCategoryResource\RelationManagers;
use App\Models\TourCategory;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\HtmlColumn;
class TourCategoryResource extends Resource
{
    protected static ?string $model = TourCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
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
        || Filament::auth()->user()->UserType === 'Admin' 
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
                
                Forms\Components\TextInput::make('parent_category_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('icon')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('parent_category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('icon')
                    ->label('Icon')
                    ->getStateUsing(fn ($record) => "<i class='{$record->icon}' style='font-size: 20px'></i>")
                    ->html(),
                Tables\Columns\TextColumn::make('display_order')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListTourCategories::route('/'),
            'create' => Pages\CreateTourCategory::route('/create'),
            'view' => Pages\ViewTourCategory::route('/{record}'),
            'edit' => Pages\EditTourCategory::route('/{record}/edit'),
        ];
    }
}
