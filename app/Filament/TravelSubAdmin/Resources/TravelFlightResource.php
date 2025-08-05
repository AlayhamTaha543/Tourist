<?php

namespace App\Filament\TravelSubAdmin\Resources;

use App\Filament\TravelSubAdmin\Resources\TravelFlightResource\Pages;
use App\Filament\TravelSubAdmin\Resources\TravelFlightResource\RelationManagers;
use App\Models\TravelAgency;
use App\Models\TravelFlight;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TravelFlightResource extends Resource
{
    protected static ?string $model = TravelFlight::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
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
            ->whereHas('agency', function ($query) {
                $query->where('admin_id', auth()->id());
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('agency_id')
                ->default(fn () => TravelAgency::where('admin_id', auth()->id())->value('id')),
                
            
            Forms\Components\TextInput::make('flight_number')
                ->label('Flight Number')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('departure_id')
                ->label('Departure Location')
                ->relationship('departure', 'name')
                ->required(),
            
            Forms\Components\Select::make('arrival_id')
                ->label('Arrival Location')
                ->relationship('arrival', 'name')
                ->required(),
            
            Forms\Components\DateTimePicker::make('departure_time')
                ->label('Departure Time')
                ->required(),
            
                Forms\Components\DateTimePicker::make('arrival_time')
                ->label('Arrival Time')
                ->required(),

                Forms\Components\TextInput::make('duration_minutes')
                ->label('Duration (minutes)')
                ->required()
                ->numeric()
              ,

            Forms\Components\TextInput::make('price')
                ->label('Price')
                ->required()
                ->numeric()
              ,

            Forms\Components\TextInput::make('available_seats')
                ->label('Available Seats')
                ->required()
                ->numeric()
              ,

            Forms\Components\Select::make('status')
                ->label('Flight Status')
                ->options([
                    'scheduled' => 'Scheduled',
                    'delayed' => 'Delayed',
                    'cancelled' => 'Cancelled',
                ])
                ->default('scheduled')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flight_number')
                    ->label('Flight Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('agency.name')
                    ->label('Travel Agency')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('departure.name')
                    ->label('Departure Location')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('arrival.name')
                    ->label('Arrival Location')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('departure_time')
                    ->label('Departure Time')
                    ->sortable(),

                Tables\Columns\TextColumn::make('arrival_time')
                    ->label('Arrival Time')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duration (minutes)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Flight Status')
                   
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
            'index' => Pages\ListTravelFlights::route('/'),
            'create' => Pages\CreateTravelFlight::route('/create'),
            'view' => Pages\ViewTravelFlight::route('/{record}'),
            'edit' => Pages\EditTravelFlight::route('/{record}/edit'),
        ];
    }
}
