<?php

namespace App\Filament\SuperAdmin\Resources\TourAdminRequestResource\Pages;

use App\Filament\SuperAdmin\Resources\TourAdminRequestResource;
use App\Models\Admin;
use App\Models\TourGuideSkill;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\TourAdminRequest;

class ViewTourAdminRequest extends ViewRecord
{
    protected static string $resource = TourAdminRequestResource::class;

    protected static string $view = 'filament.super-admin.resources.tour-admin-request-resource.pages.view-tour-admin-request';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('accept')
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

                    return redirect()->to(TourAdminRequestResource::getUrl('index'));
                }),
            Action::make('reject')
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

                    return redirect()->to(TourAdminRequestResource::getUrl('index'));
                }),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->readOnly(),
                Forms\Components\TextInput::make('email')
                    ->readOnly(),
                Forms\Components\TextInput::make('age')
                    ->readOnly(),
                Forms\Components\TagsInput::make('skills'),
                Forms\Components\FileUpload::make('personal_image')
                    ->image()
                    ->disk('public')
                    ->directory('tour_admin_requests/personal_images'),
                Forms\Components\FileUpload::make('certificate_image')
                    ->image()
                    ->disk('public')
                    ->directory('tour_admin_requests/certificate_images'),
                Forms\Components\TextInput::make('status')
                    ->readOnly(),
            ]);
    }
}
