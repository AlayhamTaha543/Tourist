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
            'location_of_tables' => $this->getSevenDayChairAvailability(),
            'reviews' => FeedbackResource::collection($this->feedbacks ?? collect()),
        ];
    }

    /**
     * Get 7-day chair availability data grouped by location and date.
     */
    /**
 * Get 7-day chair availability data grouped by location and date.
 */
private function getSevenDayChairAvailability(): array
{
    $groupedAvailability = [];
    $allChairTypes = $this->chairs()->get();

    // Generate time slots for the restaurant
    $timeSlots = $this->generateTimeSlots();

    // Initialize structure for each location
    foreach ($allChairTypes->unique('location') as $chairType) {
        $groupedAvailability[$chairType->location] = [];
    }

    for ($i = 0; $i < 7; $i++) {
        $date = Carbon::now()->addDays($i);
        $dateString = $date->toDateString();
        $dayName = $date->format('l');

        foreach ($allChairTypes as $chairType) {
            if (!isset($groupedAvailability[$chairType->location][$dateString])) {
                $groupedAvailability[$chairType->location][$dateString] = [
                    'date' => $dateString,
                    'day_name' => $dayName,
                    'time_slots' => []
                ];
            }

            foreach ($timeSlots as $timeSlot) {
                $availableCount = $chairType->availability()
                    ->where('date', $dateString)
                    ->where('time_slot', $timeSlot)
                    ->first();
                
                $availableChairs = $availableCount ? $availableCount->available_chairs_count : 0;

                if ($availableChairs > 0) { // Only include time slots with available chairs
                    $groupedAvailability[$chairType->location][$dateString]['time_slots'][] = [
                        'time' => Carbon::parse($timeSlot)->format('H:i'),
                        'available_chairs' => $availableChairs,
                        'price' => $chairType->cost,
                    ];
                }
            }
        }
    }

    // Convert associative array to indexed array for dates within each location
    foreach ($groupedAvailability as $location => $dates) {
        $groupedAvailability[$location] = array_values($dates);
    }

    // Transform the structure to include location as a property
    $result = [];
    foreach ($groupedAvailability as $location => $availability) {
        $result[] = [
            'location' => $location,
            'availability' => $availability
        ];
    }

    return $result;
}

    /**
     * Generate time slots based on restaurant hours
     */
    private function generateTimeSlots(): array
    {
        $timeSlots = [];
        
        if (!$this->opening_time || !$this->closing_time) {
            // Default time slots if not specified
            return ['12:00:00', '13:00:00', '14:00:00', '18:00:00', '19:00:00', '20:00:00', '21:00:00'];
        }
        
        $openingTime = Carbon::parse($this->opening_time);
        $closingTime = Carbon::parse($this->closing_time);
        
        $current = clone $openingTime;
        
        while ($current < $closingTime) {
            $timeSlots[] = $current->format('H:i:s');
            $current->addHour(); // 1-hour intervals
        }
        
        return $timeSlots;
    }
}
