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
            'user_fullname' => $this->user->first_name . ' ' . $this->user->last_name,
            'feedback_text' => $this->feedback_text,
            'feedback_date' => $this->feedback_date,
        ];
    }
}
