<?php

namespace App\Filament\Resources\RatingResource\Pages;

use App\Filament\Resources\RatingResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class RespondToRating extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = RatingResource::class;
    public ?array $data = [];
    
    public $record;
    protected static string $view = 'filament.resources.rating-resource.pages.respond-to-rating';
    public function mount($record): void
    {

        $this->record = \App\Models\Rating::findOrFail($record);

        $this->form->fill([
            'comment' => $this->record->comment,
            'admin_response' => $this->record->admin_response,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('comment')
                    ->label('User Comment')
                    ->disabled()
                    ->columnSpanFull(),

                Textarea::make('admin_response')
                    ->label('Your Response')
                    ->required()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $this->record->admin_response = $this->form->getState()['admin_response'];
        $this->record->save();

        Notification::make()
            ->title('Response saved successfully.')
            ->success()
            ->send();

        return redirect()->to(RatingResource::getUrl());
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->label('Submit Response')
                ->action('submit') 
                ->color('primary'),
        ];
    }


}
