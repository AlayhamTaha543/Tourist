<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Filament\Resources\PromotionResource\RelationManagers;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    
    protected static ?string $navigationGroup = 'Application Managment';
    public static function beforeCreate(Form $form, $record): void
    {
        $record->created_by = auth()->id();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('promotion_code')
                ->required()
                ->unique()
                ->maxLength(255),

            Forms\Components\Textarea::make('description'),

            Forms\Components\Select::make('discount_type')
                ->options([
                    1 => 'Percentage',
                    2 => 'Fixed Amount',
                ])
                ->required(),

            Forms\Components\TextInput::make('discount_value')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('minimum_purchase')
                ->numeric()
                ->default(0),

            Forms\Components\DateTimePicker::make('start_date')
                ->required(),

            Forms\Components\DateTimePicker::make('end_date')
                ->required(),

            Forms\Components\TextInput::make('usage_limit')
                ->numeric()
                ->nullable(),

            Forms\Components\Select::make('applicable_type')
                ->options([
                    1 => 'All',
                    2 => 'Tour',
                    3 => 'Hotel',
                    4 => 'Taxi',
                    5 => 'Restaurant',
                    6 => 'Package',
                    7 => 'Flight',
                ])
                ->nullable(),

            Forms\Components\Toggle::make('is_active')
                ->default(true),

            Forms\Components\Hidden::make('created_by')
                ->default(fn () => auth()->id()),
        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('promotion_code')->label('Code')->searchable()->sortable(),

            Tables\Columns\TextColumn::make('description')->limit(50)->wrap(),

            Tables\Columns\TextColumn::make('discount_type')
                ->label('Type')
                ->formatStateUsing(fn ($state) => $state == 1 ? 'Percentage' : 'Fixed Amount'),

            Tables\Columns\TextColumn::make('discount_value')->label('Value')->sortable(),

            Tables\Columns\TextColumn::make('minimum_purchase')->label('Min Purchase')->sortable(),

            Tables\Columns\TextColumn::make('start_date')->dateTime()->label('Start'),

            Tables\Columns\TextColumn::make('end_date')->dateTime()->label('End'),

            Tables\Columns\TextColumn::make('usage_limit')->label('Limit'),

            Tables\Columns\TextColumn::make('applicable_type')
                ->label('Applicable To')
                ->formatStateUsing(fn ($state) => [
                    1 => 'All',
                    2 => 'Tour',
                    3 => 'Hotel',
                    4 => 'Taxi',
                    5 => 'Restaurant',
                    6 => 'Package',
                    7 => 'Flight',
                ][$state] ?? 'Unknown'),

            Tables\Columns\IconColumn::make('is_active')
                ->boolean()
                ->label('Active'),

            Tables\Columns\TextColumn::make('created_by')
                ->label('Created By')
                ->formatStateUsing(fn ($state) => \App\Models\User::find($state)?->name ?? '—'),

            Tables\Columns\TextColumn::make('created_at')->since()->label('Created'),
       
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
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
