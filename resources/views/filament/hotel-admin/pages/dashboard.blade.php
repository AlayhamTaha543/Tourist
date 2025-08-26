<x-filament-panels::page>
    <div>
        <h1>Hotel Admin Dashboard</h1>
        @livewire(\App\Filament\Widgets\GenericBookingTrendWidget::class, ['modelClass' => \App\Models\HotelBooking::class, 'label' => 'Hotel Bookings Trend'])
    </div>
</x-filament-panels::page>
