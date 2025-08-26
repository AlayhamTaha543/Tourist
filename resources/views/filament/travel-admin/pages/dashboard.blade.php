<x-filament-panels::page>
    <div>
        @livewire(\App\Filament\Widgets\GenericBookingTrendWidget::class, ['modelClass' => \App\Models\TravelBooking::class, 'label' => 'Taxi Bookings Trend'])
    </div>
</x-filament-panels::page>
