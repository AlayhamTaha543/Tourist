<?php

namespace App\Filament\TravelSubAdmin\Resources;

use App\Filament\TravelSubAdmin\Resources\PackageInclusionResource\Pages;
use App\Filament\TravelSubAdmin\Resources\PackageInclusionResource\RelationManagers;
use App\Models\PackageInclusion;
use App\Models\TravelAgency;
use App\Models\TravelPackage;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageInclusionResource extends Resource
{
    protected static ?string $model = PackageInclusion::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Travel Managment';

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && Filament::auth()->user()->role === 'sub_admin' 
         && Filament::auth()->user()->section === 'travel'
         ;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
        && Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'travel';
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('package.agency', function ($query) {
                $query->where('admin_id', auth()->id());
            });
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('package_id')
                ->label('Choose Package')
                ->options(function () {
                    $agency_id = TravelAgency::where('admin_id', auth()->id())->value('id');
            
                    return TravelPackage::where('agency_id', $agency_id)
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->required(),
                Forms\Components\Select::make('inclusion_type')
                    ->label('Inclusion Type')
                    ->options([
                        1 => 'Tour',
                        2 => 'Hotel',
                        3 => 'Transport',
                        4 => 'Meal',
                        5 => 'Other',
                    ])
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_highlighted')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('package.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inclusion_type')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_highlighted')
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
            'index' => Pages\ListPackageInclusions::route('/'),
            'create' => Pages\CreatePackageInclusion::route('/create'),
            'view' => Pages\ViewPackageInclusion::route('/{record}'),
            'edit' => Pages\EditPackageInclusion::route('/{record}/edit'),
        ];
    }
}
