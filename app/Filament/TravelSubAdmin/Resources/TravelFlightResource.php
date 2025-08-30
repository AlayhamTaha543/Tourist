<?php

namespace App\Filament\TravelSubAdmin\Resources;

use App\Filament\TravelSubAdmin\Resources\TravelFlightResource\Pages;
use App\Filament\TravelSubAdmin\Resources\TravelFlightResource\RelationManagers;
use App\Models\TravelFlight;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TravelFlightResource extends Resource
{
    protected static ?string $model = TravelFlight::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                //
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
            'index' => Pages\ListTravelFlights::route('/'),
            'create' => Pages\CreateTravelFlight::route('/create'),
            'edit' => Pages\EditTravelFlight::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $admin = Auth::user();
        return parent::getEloquentQuery()->whereHas('travelAgency', function (Builder $query) use ($admin) {
            $query->where('id', $admin->travel_agency_id);
        });
    }
}
