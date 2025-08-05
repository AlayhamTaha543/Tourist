<?php

namespace App\Filament\HotelSubAdmin\Pages;

use App\Models\Hotel;
use App\Models\User;
use App\Notifications\DiscountNotification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class HotelDiscountEdit extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    public ?Hotel $hotel;
    public $discount;
    protected static string $view = 'filament.hotel-sub-admin.pages.hotel-discount-edit';
    public function mount(): void
    {
        $this->hotel = Hotel::where('admin_id', auth()->id())->firstOrFail();

        $this->form->fill([
            'discount' => $this->hotel->discount,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('discount')
                ->label('Discount ')
                ->numeric()
                ->required(),
        ];
    }

    public function save()
    {
        $this->hotel->update([
            'discount' => $this->discount,
        ]);
        if ($this->discount > 0) {
        $users = User::all();
        foreach ($users as $user) {
             $user->notify(new DiscountNotification(
                $this->hotel->name,
                $this->discount,
                'Hotel'
            ));
        }
    }

        \Filament\Notifications\Notification::make()
            ->title('  Add Discount Successfully ')
            ->success()
            ->send();
    }
}
