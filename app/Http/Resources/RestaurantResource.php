<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location ? $this->location->fullName() : null,
            'image' => $this->main_image ? asset('storage/' . $this->main_image) : null,
            'rating' => $this->average_rating,
            'price' => $this->price,
            'cuisine' => $this->cuisine,
            'opening_time' => $this->opening_time ? Carbon::parse($this->opening_time)->format('H:i') : null,
            'closing_time' => $this->closing_time ? Carbon::parse($this->closing_time)->format('H:i') : null,
            'location_of_tables' => $this->getSevenDayAvailability(),
            'reviews' => FeedbackResource::collection($this->feedbacks ?? collect()),
        ];
    }

    /**
     * Get 7-day availability data grouped by location, then date, then time slot.
     */
    private function getSevenDayAvailability(): array
    {
        $allChairs = $this->chairs()->get();
        $chairsByLocation = $allChairs->groupBy('location');
        $availability = [];
        $timeSlots = $this->generateTimeSlots();

        foreach ($chairsByLocation as $location => $chairs) {
            $locationAvailability = [];
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->addDays($i);
                $dateString = $date->toDateString();
                $daySlots = [];

                foreach ($timeSlots as $timeSlot) {
                    // Filter chairs for the current location and check availability
                    $availableChairsInLocation = collect();
                    $timeSlotFormatted = Carbon::parse($timeSlot)->format('H:i:s');

                    foreach ($chairs as $chair) {
                        $chairAvailability = $chair->availability()
                            ->where('date', $dateString)
                            ->whereTime('time_slot', $timeSlotFormatted)
                            ->first();

                        if ($chairAvailability && $chairAvailability->is_available && !$chairAvailability->is_blocked) {
                            $availableChairsInLocation->push($chair);
                        }
                    }

                    if ($availableChairsInLocation->count() > 0) {
                        $daySlots[] = [
                            'time' => Carbon::parse($timeSlot)->format('H:i'),
                            'available_chairs' => $availableChairsInLocation->count(),
                            'price' => $availableChairsInLocation->first()->cost ?? 0 // Assuming cost is consistent per location for simplicity
                        ];
                    }
                }

                if (!empty($daySlots)) {
                    $locationAvailability[] = [
                        'date' => $dateString,
                        'day_name' => $date->format('l'),
                        'time_slots' => $daySlots
                    ];
                }
            }

            if (!empty($locationAvailability)) {
                $availability[] = [
                    'location' => $location,
                    'availability' => $locationAvailability
                ];
            }
        }

        return $availability;
    }

    /**
     * Generate time slots based on restaurant hours
     */
    private function generateTimeSlots(): array
    {
        $timeSlots = [];
        
        if (!$this->opening_time || !$this->closing_time) {
            return ['12:00:00', '13:00:00', '14:00:00', '18:00:00', '19:00:00', '20:00:00', '21:00:00'];
        }
        
        $openingTime = Carbon::parse($this->opening_time);
        $closingTime = Carbon::parse($this->closing_time);
        
        $current = clone $openingTime;
        
        while ($current < $closingTime) {
            $timeSlots[] = $current->format('H:i:s');
            $current->addHour();
        }
        
        return $timeSlots;
    }

    /**
     * Get available tables for a specific slot
     */
    private function getAvailableChairsForSlot($date, $timeSlot, $location = null)
    {
        $timeSlotFormatted = Carbon::parse($timeSlot)->format('H:i:s');
        
        $query = $this->chairs()
            ->where('is_active', true)
            ->where('is_reservable', true);

        if ($location) {
            $query->where('location', $location);
        }
        
        return $query->whereDoesntHave('availability', function ($query) use ($date, $timeSlotFormatted) {
                $query->where('date', $date)
                      ->whereTime('time_slot', $timeSlotFormatted)
                      ->where(function ($q) {
                          $q->where('is_available', false)
                            ->orWhere('is_blocked', true);
                      });
            })
            ->get();
    }
}
