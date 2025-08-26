<x-filament-panels::page>
    <div>
        <h1>Taxi Admin Dashboard</h1>
        @livewire(\App\Filament\Widgets\GenericBookingTrendWidget::class, ['modelClass' => \App\Models\TaxiBooking::class, 'label' => 'Taxi Bookings Trend'])
    </div>
</x-filament-panels::page>
