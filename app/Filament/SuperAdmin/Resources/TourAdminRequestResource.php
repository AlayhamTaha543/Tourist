<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\TourAdminRequestResource\Pages;
use App\Filament\SuperAdmin\Resources\TourAdminRequestResource\RelationManagers;
use App\Models\TourAdminRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Admin;
use App\Models\TourGuideSkill;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourAdminRequestResource extends Resource
{
    protected static ?string $model = TourAdminRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('age')
                    ->required()
                    ->numeric(),
                Forms\Components\TagsInput::make('skills')
                    ->required(),
                Forms\Components\FileUpload::make('personal_image')
                    ->image()
                    ->disk('public')
                    ->directory('tour_admin_requests/personal_images')
                    ->required(),
                Forms\Components\FileUpload::make('certificate_image')
                    ->image()
                    ->disk('public')
                    ->directory('tour_admin_requests/certificate_images')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending')
                    ->readOnly()
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('skills')
                    ->badge()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('personal_image')
                    ->disk('public'),
                Tables\Columns\ImageColumn::make('certificate_image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                    }),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn(TourAdminRequest $record): bool => $record->status === 'pending')
                    ->action(function (TourAdminRequest $record) {
                        $password = Str::random(10); // Generate a random password
                        $hashedPassword = Hash::make($password);

                        $admin = Admin::create([
                            'name' => $record->full_name,
                            'email' => $record->email,
                            'password' => $hashedPassword,
                            'role' => 'admin',
                            'section' => 'tour',
                            'image' => $record->personal_image,
                        ]);

                        TourGuideSkill::create([
                            'admin_id' => $admin->id,
                            'skills' => $record->skills,
                            'age' => $record->age,
                        ]);

                        $record->update(['status' => 'accepted']);

                        Notification::make()
                            ->title('Tour Admin Request Accepted')
                            ->body("The request from {$record->full_name} has been accepted. Login email: {$record->email}, Temporary password: {$password}")
                            ->success()
                            ->sendToDatabase($admin); // Send notification to the new admin

                        Notification::make()
                            ->title('Tour Admin Request Accepted')
                            ->body("The request from {$record->full_name} has been accepted.")
                            ->success()
                            ->send(); // Send a general success notification to the super admin
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn(TourAdminRequest $record): bool => $record->status === 'pending')
                    ->action(function (TourAdminRequest $record) {
                        // Delete uploaded images
                        Storage::disk('public')->delete($record->personal_image);
                        Storage::disk('public')->delete($record->certificate_image);

                        $record->update(['status' => 'rejected']);

                        Notification::make()
                            ->title('Tour Admin Request Rejected')
                            ->body("The request from {$record->full_name} has been rejected.")
                            ->danger()
                            ->send(); // Send a general rejection notification to the super admin
                    }),
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
            'index' => Pages\ListTourAdminRequests::route('/'),
            'create' => Pages\CreateTourAdminRequest::route('/create'),
            'view' => Pages\ViewTourAdminRequest::route('/{record}'),
            'edit' => Pages\EditTourAdminRequest::route('/{record}/edit'),
        ];
    }
}
