<?php

namespace App\Filament\TourSubAdmin\Resources\PackageBookingResource\Pages;

use App\Filament\TourSubAdmin\Resources\PackageBookingResource;
use App\Models\Booking;
use App\Models\PackageBooking;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\ActionSize;

class CreatePackageBooking extends CreateRecord
{
    protected static string $resource = PackageBookingResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['cost'] = PackageBookingResource::calculateFinalCost(
        $data['package_id'],
        $data['number_of_adults'],
        $data['number_of_children'],
        $data['promotion_code'] ?? null
    );

    return $data;
}

protected function getFormActions(): array
{
    return [
        Action::make('confirmBooking')
            ->label('Book now')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(' confirm Booking')
            ->modalSubheading(fn () => 'Are you sure you want to confirm Booking , The final Price is:' . $this->form->getState()['cost'] . ' USD')
            ->action(function () {
                $data = $this->form->getState();

                PackageBooking::create($data);

                Notification::make()
                    ->title('confirm booking successfuly!')
                    ->success()
                    ->send();

                $this->redirect(PackageBookingResource::getUrl());
            }),
    ];
}
}
