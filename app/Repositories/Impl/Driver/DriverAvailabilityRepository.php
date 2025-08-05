<?php

namespace App\Repositories\Impl\Driver;

use App\Models\Driver;
use App\Repositories\Interfaces\Taxi\DriverAvailabilityRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class DriverAvailabilityRepository implements DriverAvailabilityRepositoryInterface
{
    public function setOnline(int $driverId): void
    {
        $this->updateAvailability($driverId, 'available');
    }

    public function setOffline(int $driverId): void
    {
        $this->updateAvailability($driverId, 'offline');
    }

    public function updateAvailability(int $driverId, string $status): bool
    {
        if (!in_array($status, ['available', 'busy', 'offline'])) {
            throw new InvalidArgumentException('Invalid status');
        }

        return Driver::where('id', $driverId)->update([
            'availability_status' => $status
        ]) > 0;
    }

    public function isOnline(int $driverId): bool
    {
        return Driver::where('id', $driverId)
            ->where('availability_status', 'available')
            ->exists();
    }

    public function getAvailableDrivers(): Collection
    {
        return Driver::where('availability_status', 'available')->get();
    }

    public function getDriversInShift(): Collection
    {
        return Driver::whereTime('shift_start', '<=', now()->format('H:i:s'))
            ->whereTime('shift_end', '>=', now()->format('H:i:s'))
            ->where('availability_status', 'available')
            ->get();
    }

    public function getDriversByStatus(string $status): Collection
    {
        $validStatuses = ['available', 'busy', 'offline'];

        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException(
                "Invalid status: $status. Valid values are: " . implode(', ', $validStatuses)
            );
        }

        return Driver::query()
            ->where('availability_status', $status)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function getAvailableDriversWithinShift(string $time): Collection
    {
        $time = $this->parseTime($time);

        return Driver::query()
            ->where('availability_status', 'available')
            ->where('is_active', true)
            ->where(function ($query) use ($time) {
                $query->where(function ($q) use ($time) {
                    // Normal shift
                    $q->whereColumn('shift_start', '<=', 'shift_end')
                        ->whereTime('shift_start', '<=', $time)
                        ->whereTime('shift_end', '>=', $time);
                })->orWhere(function ($q) use ($time) {
                    // Overnight shift
                    $q->whereColumn('shift_start', '>', 'shift_end')
                        ->where(function ($q2) use ($time) {
                        $q2->whereTime('shift_start', '<=', $time)
                            ->orWhereTime('shift_end', '>=', $time);
                    });
                });
            })
            ->orderBy('id')
            ->get();
    }

    public function isDriverAvailableAtTime(int $driverId, Carbon $bookingTime): bool
    {
        $time = $bookingTime->toTimeString();

        return Driver::where('id', $driverId)
            ->where('availability_status', 'available')
            ->where(function ($query) use ($time) {
                $query->where(function ($q) use ($time) {
                    // Normal shift
                    $q->whereColumn('shift_start', '<=', 'shift_end')
                        ->whereTime('shift_start', '<=', $time)
                        ->whereTime('shift_end', '>=', $time);
                })->orWhere(function ($q) use ($time) {
                    // Overnight shift
                    $q->whereColumn('shift_start', '>', 'shift_end')
                        ->where(function ($q2) use ($time) {
                        $q2->whereTime('shift_start', '<=', $time)
                            ->orWhereTime('shift_end', '>=', $time);
                    });
                });
            })
            ->whereDoesntHave('trips', function ($query) use ($bookingTime) {
                $query->where(function ($q) use ($bookingTime) {
                    $q->whereBetween('started_at', [
                        $bookingTime->copy()->subHour(),
                        $bookingTime->copy()->addHour()
                    ])->orWhereNull('completed_at');
                });
            })
            ->exists();
    }

    private function parseTime(string $time): Carbon
    {
        try {
            return Carbon::createFromFormat('H:i:s', $time);
        } catch (\Exception) {
            try {
                return Carbon::createFromFormat('H:i', $time);
            } catch (\Exception $e) {
                throw new InvalidArgumentException("Invalid time format. Expected H:i or H:i:s.");
            }
        }
    }
}
