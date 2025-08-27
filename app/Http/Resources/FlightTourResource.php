<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AdminResource;
use App\Http\Resources\TourScheduleResource;
use Carbon\Carbon;

class FlightTourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultImage = "images/admin/a.png"; // This should ideally be a config value

        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_price' => $this->base_price,
            'admin' => $this->admin ? new AdminResource($this->admin) : null,
            'schedules' => TourScheduleResource::collection($this->schedules),
        ];
    }
}