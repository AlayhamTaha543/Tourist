<x-filament-panels::page>
    <div>
        <h1>Rent Admin Dashboard</h1>
        @livewire(\App\Filament\Widgets\GenericBookingTrendWidget::class, ['modelClass' => \App\Models\RentalBooking::class, 'label' => 'Rental Bookings Trend'])
    </div>
</x-filament-panels::page>
