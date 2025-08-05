<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxiBookingResource extends JsonResource
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
            'booking_id' => $this->booking_id,
            'user_id' => $this->booking->user_id,
            'taxi_service_id' => $this->TaxiServiceID,
            'vehicle_type_id' => $this->VehicleTypeID,
            'vehicle_id' => $this->VehicleID,
            'driver_id' => $this->DriverID,
            'pickup_location' => $this->PickupLocation,
            'dropoff_location' => $this->DropoffLocation,
            'pickup_datetime' => $this->PickupDateTime?->toIso8601String(),
            'dropoff_datetime' => $this->DropoffDateTime?->toIso8601String(),
            'status' => $this->status,
            'fare_amount' => (float) $this->FareAmount,
            'distance_km' => (float) $this->DistanceKM,
            'cancellation_fee' => (float) $this->CancellationFee,
            'payment_status' => $this->PaymentStatus,
            'payment_method' => $this->PaymentMethod,
            'special_requests' => $this->SpecialRequests,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Include user data if relationship is loaded
            'user' => $this->when($this->relationLoaded('booking.user'), function () {
                return [
                    'id' => $this->booking->user->id,
                    'name' => $this->booking->user->name,
                    'email' => $this->booking->user->email,
                ];
            }),

            // Include taxi service data if relationship is loaded
            'taxi_service' => $this->when($this->relationLoaded('taxiService'), function () {
                return new TaxiServiceResource($this->taxiService);
            }),

            // Include vehicle data if relationship is loaded
            'vehicle' => $this->when($this->relationLoaded('vehicle'), function () {
                return new VehicleResource($this->vehicle);
            }),

            // Include driver data if relationship is loaded
            'driver' => $this->when($this->relationLoaded('driver'), function () {
                return new DriverResource($this->driver);
            }),

            // Include ratings if relationship is loaded
            'ratings' => $this->when($this->relationLoaded('ratings'), function () {
                return RatingResource::collection($this->ratings);
            }),

            // HATEOAS links
            'links' => [
                'self' => route('api.taxi-bookings.show', $this->id),
            ],
        ];
    }
}
