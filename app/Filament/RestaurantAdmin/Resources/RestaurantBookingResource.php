<?php

namespace App\Filament\RestaurantAdmin\Resources;

use App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource\Pages;
use App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource\RelationManagers;
use App\Models\RestaurantBooking;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RestaurantBookingResource extends Resource
{
    protected static ?string $model = RestaurantBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Booking Managment';
    
    protected static ?int $navigationSort = 3;
    public static function canAccess(): bool
    {
        return Filament::auth()->check()  
        &&((Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'restaurant')
        ||(Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'restaurant'));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
        &&((Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'restaurant')
        ||(Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'restaurant'));
    }
    public static function getEloquentQuery(): Builder
    {
        if (Filament::auth()->user()->role === 'admin') {
            return parent::getEloquentQuery();
        }
        return parent::getEloquentQuery()
        ->whereHas('restaurant', function ($query) {
            $query->where('admin_id', auth()->id());
        });
    }
    public static function form(Form $form): Form
    {
        return $form
        
            ->schema([
                Forms\Components\Select::make('booking_id')
                    ->relationship('booking','id')
                    ->required()
                    ->searchable()
                    ->preload()
                    // ->multiple()
                    ->native(false),
                Forms\Components\Select::make('restaurant_id')
                    ->relationship('restaurant','name')
                    ->required()
                    ->searchable()
                    ->preload()
                    // ->multiple()
                    ->native(false),
                Forms\Components\Select::make('table_id')
                    ->relationship('table','number')
                    ->required()
                    ->searchable()
                    ->preload()
                    // ->multiple()
                    ->native(false),
                    
                Forms\Components\DatePicker::make('reservation_date')
                    ->required(),
                Forms\Components\TimePicker::make('reservation_time')
                    ->required(),
                Forms\Components\TextInput::make('number_of_guests')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('duration')
                    ->required()
                    ->numeric()
                    ->default(120),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        
            ->columns([
                Tables\Columns\TextColumn::make('user.first_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('table_id')
                    ->numeric()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('booking.status')
                    ->label('Status')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('booking.payment_status')
                    ->label('payment status')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('booking.discount_amount')
                    ->label('discount')
                        ->numeric()
                        ->sortable(),
                Tables\Columns\TextColumn::make('reservation_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reservation_time'),
                Tables\Columns\TextColumn::make('order')
                ->label('Order Details')
                ->formatStateUsing(function ($state) {
                    $items = json_decode($state, true);

                    if (!$items || !is_array($items)) {
                        return 'No order';
                    }

                    return collect($items)->map(function ($item) {
                        return "{$item['quantity']} × {$item['name']} ({$item['subtotal']}₺)";
                    })->implode("\n");
                })
                ->tooltip(fn ($state) => strip_tags($state)) 
                ->wrap(),
                Tables\Columns\TextColumn::make('cost'),
                Tables\Columns\TextColumn::make('numberOfGuests')
                    ->numeric()
                    ->sortable(),
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
    public static function canCreate(): bool
    {
        return false; 
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestaurantBookings::route('/'),
            // 'create' => Pages\CreateRestaurantBooking::route('/create'),
            'view' => Pages\ViewRestaurantBooking::route('/{record}'),
            'edit' => Pages\EditRestaurantBooking::route('/{record}/edit'),
        ];
    }
}
