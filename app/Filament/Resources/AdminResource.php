<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Filament\Resources\AdminResource\RelationManagers;
use App\Models\Admin;
use App\Notifications\OTPNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                    Forms\Components\Select::make('role')
                    ->options(function () {
                        $user = auth()->user();
                        if ($user->role === 'super_admin') {
                            return [
                                'super_admin' => 'Super Admin',
                                'admin' => 'Admin',
                                'sub_admin' => 'Sub Admin',
                            ];
                        }
                        if ($user->role === 'admin' || $user->role === 'sub_admin') {
                            return [
                                'sub_admin' => 'Sub Admin',
                            ];
                        }
    
                        return [];
                    })
                    ->required(),
                    Forms\Components\TextInput::make('section')
                    ->default(fn () => auth()->user()?->section)
                    ->disabled()
                    ->dehydrated(true),
                ]);
    }

    // public static function afterCreate(Admin $record): void
    // {
    //     $record->code = rand(1000, 9999);
    //     $record->code_expires_at = now()->addMinutes(6);
    //     $record->save();

    //     $record->notify(new OTPNotification());
    // }
    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),
            Tables\Columns\TextColumn::make('email')
                ->searchable(),
            Tables\Columns\TextColumn::make('email_verified_at')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('role')
                ->searchable(),
            Tables\Columns\TextColumn::make('section')
                ->searchable(),
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
            
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ])
        ->modifyQueryUsing(function ($query) {
            $user = auth()->user();
            
            if ($user->role === 'admin') {
                $query->where('section', $user->section);
            }
            if ($user->role === 'sub_admin') {
                $query->where('section', $user->section);
            }

        });
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'view' => Pages\ViewAdmin::route('/{record}'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
    
}
