<?php

namespace App\Filament\HotelSubAdmin\Resources;

use App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource\Pages;
use App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource\RelationManagers;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomAvaibilityResource extends Resource
{
    protected static ?string $model = RoomAvailability::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Room Managment';
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->whereHas('roomType.hotel', function ($query) {
            $query->where('admin_id', auth()->id());
        });
    }
    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                    Forms\Components\Select::make('room_type_id')
                        ->label('Room Type')
                        ->relationship('roomType', 'name', modifyQueryUsing: fn ($query) =>
                            $query->whereHas('hotel', fn ($q) =>
                                $q->where('admin_id', auth()->id())
                            )
                        )
                        ->required(),
        
                    Forms\Components\DatePicker::make('date')
                        ->label('Date')
                        ->required(),
        
                    Forms\Components\TextInput::make('available_rooms')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
        
                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('$'),
        
                    Forms\Components\Toggle::make('is_blocked')
                        ->label('Blocked')
                        ->default(false),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->headerActions([
            Action::make('addNextDayAvailability')
                ->label('Add avability For new day')
                ->icon('heroicon-o-calendar')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirm ')
                ->modalSubheading('Add avability For new day')
                ->action(function () {
                    $latestDate = RoomAvailability::max('date');
                    $nextDate = $latestDate
                        ? Carbon::parse($latestDate)->addDay()
                        : now()->startOfDay();

                    $roomTypes = RoomType::all();

                    foreach ($roomTypes as $roomType) {
                        RoomAvailability::create([
                            'room_type_id' => $roomType->id,
                            'date' => $nextDate->toDateString(),
                            'available_rooms' => $roomType->number,
                            'price' => $roomType->base_price,
                            'is_blocked' => false,
                        ]);
                    }

                    Notification::make()
                        ->title('Successfuly Added Avability Room in ' . $nextDate->toDateString())
                        ->success()
                        ->send();
                })
        ])
            ->columns([
                Tables\Columns\TextColumn::make('roomType.name')
                    ->label('Room Type')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_rooms')
                    ->label('Available Rooms'),

                Tables\Columns\TextColumn::make('price')
                    ->money('USD', true), 

                Tables\Columns\IconColumn::make('is_blocked')
                    ->boolean()
                    ->label('Blocked'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('increase')
                    ->label('+1')
                    ->color('success')
                    ->icon('heroicon-m-plus')
                    ->action(function (RoomAvailability $record) {
                        $maxRooms = $record->roomType->number;

                        if ($record->available_rooms < $maxRooms) {
                            $record->increment('available_rooms');
                        }
                    })
                    ->requiresConfirmation()
                    ->visible(fn (RoomAvailability $record) =>
                        $record->available_rooms < $record->roomType->number
                    ),

                Action::make('decrease')
                    ->label('-1')
                    ->color('danger')
                    ->icon('heroicon-m-minus')
                    ->action(function (RoomAvailability $record) {
                        if ($record->available_rooms > 0) {
                            $record->decrement('available_rooms');
                        }
                    })
                    ->requiresConfirmation()
                    ->visible(fn (RoomAvailability $record) =>
                        $record->available_rooms > 0
                    ),
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
            'index' => Pages\ListRoomAvaibilities::route('/'),
            'create' => Pages\CreateRoomAvaibility::route('/create'),
            'view' => Pages\ViewRoomAvaibility::route('/{record}'),
            'edit' => Pages\EditRoomAvaibility::route('/{record}/edit'),
        ];
    }
}
