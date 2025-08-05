<?php

namespace App\Filament\TourSubAdmin\Resources;

use App\Filament\TourSubAdmin\Resources\PackageBookingResource\Pages;
use App\Filament\TourSubAdmin\Resources\PackageBookingResource\RelationManagers;
use App\Models\PackageBooking;
use App\Models\Promotion;
use App\Models\TravelPackage;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class PackageBookingResource extends Resource
{
    protected static ?string $model = PackageBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Booking Managment';

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
    public static function calculateBaseCost($package_id, $number_of_adults, $number_of_children)
    {
        $package = TravelPackage::find($package_id);
        if (!$package) return 0;

        return $package->base_price * ($number_of_adults + $number_of_children);
    }

    public static function applyPromotion($cost, $promotionCode): float
    {
        $promotion = Promotion::where('promotion_code', $promotionCode)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($q) {
                $q->where('applicable_type', 1)
                ->orWhere('applicable_type', 3);
            })
            ->first();

        if ($promotion) {
            return $cost - ($cost * $promotion->discount_value / 100);
        }

        return -1; 
    }

    public static function calculateFinalCost($package_id, $number_of_adults, $number_of_children, $promotion_code = null)
    {
        $base_cost = self::calculateBaseCost($package_id, $number_of_adults, $number_of_children);

        if ($promotion_code) {
            $final = self::applyPromotion($base_cost, $promotion_code);
            return $final !== -1 ? $final : $base_cost;
        }

        return $base_cost;
    }

public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Hidden::make('tour_id')
            ->default(fn () => \App\Models\Tour::where('admin_id', auth()->id())->value('id'))
            ->dehydrated(),
        Forms\Components\Select::make('package_id')
            ->relationship('package', 'name')
            ->required()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $adults = (int) $get('number_of_adults') ?? 1;
                $children = (int) $get('number_of_children') ?? 0;
                $promotion_code = $get('promotion_code');
                $set('cost', self::calculateFinalCost($state, $adults, $children, $promotion_code));

                $package = TravelPackage::find($state);
                if ($package) {
                    $set('agency_id', $package->agency_id);
                }
            }),

        Forms\Components\Hidden::make('agency_id'),

        Forms\Components\TextInput::make('number_of_adults')
            ->numeric()
            ->default(1)
            ->minValue(1)
            ->live()
            ->required()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $package_id = $get('package_id');
                $children = (int) $get('number_of_children') ?? 0;
                $promotion_code = $get('promotion_code');
                $set('cost', self::calculateFinalCost($package_id, $state, $children, $promotion_code));
            }),

        Forms\Components\TextInput::make('number_of_children')
            ->numeric()
            ->default(0)
            ->live()
            ->required()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $package_id = $get('package_id');
                $adults = (int) $get('number_of_adults') ?? 1;
                $promotion_code = $get('promotion_code');
                $set('cost', self::calculateFinalCost($package_id, $adults, $state, $promotion_code));
            }),

        Forms\Components\TextInput::make('promotion_code')
            ->label('Promotion Code')
            ->nullable()
            ->live()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $package_id = $get('package_id');
                $adults = (int) $get('number_of_adults') ?? 1;
                $children = (int) $get('number_of_children') ?? 0;

                $cost_before = self::calculateBaseCost($package_id, $adults, $children);
                $final_cost = self::applyPromotion($cost_before, $state);

                if ($final_cost === -1) {
                    Notification::make()
                        ->title('Invalid or expired promotion code.')
                        ->danger()
                        ->send();

                    $set('cost', $cost_before);
                } else {
                    $set('cost', $final_cost);
                }
            }),

            Forms\Components\TextInput::make('cost')
            ->numeric()
            ->prefix('USD')
            ->required()
            ->disabled()
            ->dehydrated(),
        ]); 
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('package_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_adults')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_children')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
        ->label('Pay')
        ->icon('heroicon-m-currency-dollar')
        ->color('success')
        ->form([
            \Filament\Forms\Components\TextInput::make('password')
                ->label('Password ')
                ->password()
                ->required(),
        ])
        ->action(function (array $data, $record) {
            if (!\Hash::check($data['password'], Filament::auth()->user()->password)) {
                Notification::make()
                    ->title('  Incorrect Password ')
                    ->danger()
                    ->send();

                return;
            }

            $record->update(['payment_status' => 'paid']);

            Notification::make()
                ->title('Payed Successfuly  !')
                ->success()
                ->send();
        })
        ->modalHeading('confirm Paying ')
        ->modalSubmitActionLabel('Confirm Paying ')
        ->visible(fn ($record) => $record->payment_status !== 'paid'),

        Tables\Actions\DeleteAction::make()
            ->visible(fn ($record) => $record->payment_status !== 'paid'),
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
            'index' => Pages\ListPackageBookings::route('/'),
            'create' => Pages\CreatePackageBooking::route('/create'),
            'view' => Pages\ViewPackageBooking::route('/{record}'),
            'edit' => Pages\EditPackageBooking::route('/{record}/edit'),
        ];
    }
}
