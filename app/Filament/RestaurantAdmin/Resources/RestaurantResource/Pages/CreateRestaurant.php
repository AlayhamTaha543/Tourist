<?php

namespace App\Filament\RestaurantAdmin\Resources\RestaurantResource\Pages;

use App\Filament\RestaurantAdmin\Resources\RestaurantResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Location;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurant extends CreateRecord
{
    protected static string $resource = RestaurantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $country = Country::firstOrCreate(
            ['name' => $data['country']],
            ['code' => strtoupper(substr($data['country'], 0, 3))]
        );
        $city = City::firstOrCreate(
            ['name' => $data['city'], 'country_id' => $country->id]
        );
        $data['city_id'] = $city->id;

        $location = Location::create([
            'name' => $data['name'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'city_id' => $city->id,
            'region' => $data['region'] ?? null,
            'is_popular' => $data['is_popular'] ?? false,
        ]);
        $data['location_id'] = $location->id;
        return $data;
    }
}
