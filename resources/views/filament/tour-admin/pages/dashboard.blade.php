<x-filament-panels::page>
    <div>
        @livewire(\App\Filament\Widgets\GenericBookingTrendWidget::class, ['modelClass' => \App\Models\TourBooking::class, 'label' => 'Taxi Bookings Trend'])
    </div>
</x-filament-panels::page>
