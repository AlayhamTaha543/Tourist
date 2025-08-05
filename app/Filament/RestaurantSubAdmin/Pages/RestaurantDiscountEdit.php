<?php

namespace App\Filament\RestaurantSubAdmin\Pages;

use App\Models\Restaurant;
use App\Models\User;
use App\Notifications\DiscountNotification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Notification;

class RestaurantDiscountEdit extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public ?Restaurant $restaurant;

    public $discount;
    protected static string $view = 'filament.restaurant-sub-admin.pages.restaurant-discount-edit';
    public function mount(): void
    {
        $this->restaurant = Restaurant::where('admin_id', auth()->id())->firstOrFail();
        $this->form->fill([
            'discount' => $this->restaurant->discount,
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
        $this->restaurant->update([
            'discount' => $this->discount,
        ]);
        if ($this->discount > 0) {
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new DiscountNotification(
                $this->restaurant->name,
                $this->discount,
                'Restaurant' 
            ));
        }
    }
        
        \Filament\Notifications\Notification::make()
            ->title('  Add Discount Successfully ')
            ->success()
            ->send();
    }
    

}
