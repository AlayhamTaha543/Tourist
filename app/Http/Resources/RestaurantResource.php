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
            'availability' => $this->getSevenDayAvailability(),
            'reviews' => FeedbackResource::collection($this->feedbacks ?? collect()),
        ];
    }

    /**
     * Get 7-day availability data similar to hotel structure
     */
    private function getSevenDayAvailability(): array
    {
        $availability = [];
        
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i);
            $dateString = $date->toDateString();
            
            $timeSlots = $this->generateTimeSlots();
            $daySlots = [];
            
            foreach ($timeSlots as $timeSlot) {
                $availableTables = $this->getAvailableTablesForSlot($dateString, $timeSlot);
                
                if ($availableTables->count() > 0) {
                    $groupedTables = $availableTables->groupBy('location');
                    $locationData = [];
                    
                    foreach ($groupedTables as $location => $tables) {
                        $locationData[] = [
                            'location' => $location,
                            'available_tables' => $tables->count(),
                            'base_price' => $tables->first()->cost ?? 0
                        ];
                    }
                    
                    $daySlots[] = [
                        'time' => Carbon::parse($timeSlot)->format('H:i'),
                        'total_available_tables' => $availableTables->count(),
                        'locations' => $locationData
                    ];
                }
            }
            
            // Only include days with available slots
            if (!empty($daySlots)) {
                $availability[] = [
                    'date' => $dateString,
                    'day_name' => $date->format('l'),
                    'time_slots' => $daySlots
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
    private function getAvailableTablesForSlot($date, $timeSlot)
    {
        $timeSlotFormatted = Carbon::parse($timeSlot)->format('H:i:s');
        
        return $this->tables()
            ->where('is_active', true)
            ->where('is_reservable', true)
            ->whereDoesntHave('availability', function ($query) use ($date, $timeSlotFormatted) {
                $query->where('date', $date)
                      ->where('time_slot', $timeSlotFormatted)
                      ->where(function ($q) {
                          $q->where('is_available', false)
                            ->orWhere('is_blocked', true);
                      });
            })
            ->get();
    }
}
