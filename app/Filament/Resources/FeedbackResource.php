<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Filament\Resources\FeedbackResource\RelationManagers;
use App\Models\Feedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationGroup = 'Application Managment';

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
                    Tables\Columns\TextColumn::make('user.first_name')->label('User')->default('Guest'),
                    Tables\Columns\TextColumn::make('feedback_text')->label('Feedback')->wrap(),
                    Tables\Columns\TextColumn::make('feedback_type')->label('Type')->formatStateUsing(fn ($state) => match ($state) {
                        1 => 'App', 2 => 'Service', 3 => 'Other',
                    }),
                    Tables\Columns\TextColumn::make('status')->label('Status')->formatStateUsing(fn ($state) => match ($state) {
                        1 => 'Unread', 2 => 'Read', 3 => 'Responded',
                    }),
                    Tables\Columns\TextColumn::make('feedback_date')->label('Submitted At')->dateTime(),
                ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markAsRead')
                ->label('Mark as Read')
                ->icon('heroicon-o-eye') 
                ->visible(fn ($record) => $record->status == 1)
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update(['status' => 2]); 
                })
                ->color('success'),
                
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
            'index' => Pages\ListFeedback::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}
