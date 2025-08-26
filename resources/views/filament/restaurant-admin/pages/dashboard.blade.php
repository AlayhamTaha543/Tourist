<x-filament-panels::page>
    <div>
        <h1>Restaurant Admin Dashboard</h1>
        @livewire(\App\Filament\Widgets\GenericBookingTrendWidget::class, ['modelClass' => \App\Models\RestaurantBooking::class, 'label' => 'Restaurant Bookings Trend'])
    </div>
</x-filament-panels::page>
