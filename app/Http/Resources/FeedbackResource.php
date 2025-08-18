<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fullname' => $this->user->first_name . ' ' . $this->user->last_name,
            'text' => $this->feedback_text,
            'date' => $this->feedback_date,
        ];
    }
}
