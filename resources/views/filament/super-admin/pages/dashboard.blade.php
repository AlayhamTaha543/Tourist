<x-filament-panels::page>
    <div>
        <h1>Super Admin Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @livewire(\App\Filament\SuperAdmin\Widgets\AdminsAndUsersTrendWidget::class)
            @livewire(\App\Filament\SuperAdmin\Widgets\ServicesTrendWidget::class)
            @livewire(\App\Filament\SuperAdmin\Widgets\BookingsCountTrendWidget::class)
            @livewire(\App\Filament\SuperAdmin\Widgets\PaymentsAmountTrendWidget::class)
        </div>
    </div>
</x-filament-panels::page>
