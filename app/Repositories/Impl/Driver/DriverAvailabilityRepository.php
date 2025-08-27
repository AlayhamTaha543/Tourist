<?php

namespace App\Repositories\Impl\Driver;

use App\Models\Driver;
use App\Models\TaxiBooking;
use App\Repositories\Interfaces\Taxi\DriverAvailabilityRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $updated = Driver::where('id', $driverId)->update([
            'availability_status' => $status
        ]) > 0;

        if ($updated) {
            Log::info("Driver availability updated", [
                'driver_id' => $driverId,
                'status' => $status
            ]);
        }

        return $updated;
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
            ->exists();
    }

    /**
     * IMPROVED: Check if driver is available for booking with comprehensive overlap detection
     */
    public function isDriverAvailableForBooking(int $driverId, Carbon $pickupDateTime, int $durationMinutes): bool
    {
        $bookingStart = $pickupDateTime;
        $bookingEnd = $pickupDateTime->copy()->addMinutes($durationMinutes);

        Log::debug("Checking driver availability for booking", [
            'driver_id' => $driverId,
            'booking_start' => $bookingStart->toDateTimeString(),
            'booking_end' => $bookingEnd->toDateTimeString(),
            'duration_minutes' => $durationMinutes
        ]);

        // Step 1: Check basic driver availability and shift times
        $driver = Driver::where('id', $driverId)
            ->where('availability_status', 'available')
            ->where('is_active', true)
            ->first();

        if (!$driver) {
            Log::debug("Driver {$driverId} not available or not active");
            return false;
        }

        // Step 2: Check shift times - ensure booking falls within driver's working hours
        $bookingStartTime = $bookingStart->format('H:i:s');
        $bookingEndTime = $bookingEnd->format('H:i:s');

        $withinShift = $this->isTimeWithinShift($driver, $bookingStartTime, $bookingEndTime);
        if (!$withinShift) {
            Log::debug("Driver {$driverId} booking outside shift hours", [
                'shift_start' => $driver->shift_start,
                'shift_end' => $driver->shift_end,
                'booking_start_time' => $bookingStartTime,
                'booking_end_time' => $bookingEndTime
            ]);
            return false;
        }

        // Step 3: CRITICAL - Check for overlapping bookings with comprehensive logic
        $hasOverlappingBookings = TaxiBooking::where('driver_id', $driverId)
            ->where(function ($query) {
                // Only check active bookings
                $query->whereNotIn('status', ['cancelled', 'completed']);
            })
            ->where(function ($query) use ($bookingStart, $bookingEnd) {
                // Comprehensive overlap detection - covers ALL possible overlap scenarios
                $query->where(function ($q) use ($bookingStart, $bookingEnd) {
                    // Scenario 1: Existing booking starts within new booking period
                    $q->whereBetween('pickup_date_time', [$bookingStart, $bookingEnd]);
                })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                    // Scenario 2: Existing booking ends within new booking period
                    $q->whereBetween(
                        DB::raw('DATE_ADD(pickup_date_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE)'),
                        [$bookingStart, $bookingEnd]
                    );
                })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                    // Scenario 3: New booking falls completely within existing booking
                    $q->where('pickup_date_time', '<=', $bookingStart)
                        ->where(
                            DB::raw('DATE_ADD(pickup_date_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE)'),
                            '>=',
                            $bookingEnd
                        );
                })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                    // Scenario 4: Existing booking falls completely within new booking
                    $q->where('pickup_date_time', '>=', $bookingStart)
                        ->where(
                            DB::raw('DATE_ADD(pickup_date_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE)'),
                            '<=',
                            $bookingEnd
                        );
                });
            })
            ->exists();

        if ($hasOverlappingBookings) {
            // Log the conflicting bookings for debugging
            $conflictingBookings = TaxiBooking::where('driver_id', $driverId)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->where(function ($query) use ($bookingStart, $bookingEnd) {
                    $query->where(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->whereBetween('pickup_date_time', [$bookingStart, $bookingEnd]);
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->whereBetween(
                            DB::raw('DATE_ADD(pickup_date_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE)'),
                            [$bookingStart, $bookingEnd]
                        );
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('pickup_date_time', '<=', $bookingStart)
                            ->where(
                                DB::raw('DATE_ADD(pickup_date_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE)'),
                                '>=',
                                $bookingEnd
                            );
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('pickup_date_time', '>=', $bookingStart)
                            ->where(
                                DB::raw('DATE_ADD(pickup_date_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE)'),
                                '<=',
                                $bookingEnd
                            );
                    });
                })
                ->get(['id', 'pickup_date_time', 'duration_minutes', 'status']);

            Log::debug("Driver {$driverId} has overlapping bookings", [
                'conflicting_bookings' => $conflictingBookings->toArray(),
                'requested_booking_start' => $bookingStart->toDateTimeString(),
                'requested_booking_end' => $bookingEnd->toDateTimeString()
            ]);

            return false;
        }

        Log::debug("Driver {$driverId} is available for booking");
        return true;
    }

    /**
     * Check if booking time falls within driver's shift
     */
    private function isTimeWithinShift(Driver $driver, string $bookingStartTime, string $bookingEndTime): bool
    {
        if (!$driver->shift_start || !$driver->shift_end) {
            // No shift defined, assume 24/7 availability
            return true;
        }

        $shiftStart = Carbon::parse($driver->shift_start);
        $shiftEnd = Carbon::parse($driver->shift_end);
        $bookingStart = Carbon::parse($bookingStartTime);
        $bookingEnd = Carbon::parse($bookingEndTime);

        // Handle overnight shifts (e.g., 22:00 to 06:00)
        if ($shiftEnd->lessThan($shiftStart)) {
            // Overnight shift
            return ($bookingStart->greaterThanOrEqualTo($shiftStart) || $bookingStart->lessThanOrEqualTo($shiftEnd)) &&
                ($bookingEnd->greaterThanOrEqualTo($shiftStart) || $bookingEnd->lessThanOrEqualTo($shiftEnd));
        } else {
            // Regular shift (same day)
            return $bookingStart->greaterThanOrEqualTo($shiftStart) &&
                $bookingEnd->lessThanOrEqualTo($shiftEnd);
        }
    }

    /**
     * Get all active bookings for a driver within a time period
     */
    public function getDriverBookingsInPeriod(int $driverId, Carbon $startTime, Carbon $endTime): Collection
    {
        return TaxiBooking::where('driver_id', $driverId)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('pickup_date_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('pickup_date_time', '<', $startTime)
                            ->where(DB::raw('DATE_ADD(pickup_date_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE)'), '>', $startTime);
                    });
            })
            ->orderBy('pickup_date_time')
            ->get();
    }

    /**
     * Find the next available time slot for a driver
     */
    public function findNextAvailableSlot(int $driverId, Carbon $preferredTime, int $durationMinutes, int $maxHoursAhead = 24): ?Carbon
    {
        $currentTime = $preferredTime->copy();
        $maxTime = $preferredTime->copy()->addHours($maxHoursAhead);

        // Check every 15 minutes
        while ($currentTime->lessThan($maxTime)) {
            if ($this->isDriverAvailableForBooking($driverId, $currentTime, $durationMinutes)) {
                return $currentTime;
            }

            $currentTime->addMinutes(15);
        }

        return null;
    }

    /**
     * Get driver availability statistics for a time period
     */
    public function getDriverAvailabilityStats(int $driverId, Carbon $startDate, Carbon $endDate): array
    {
        $totalBookings = TaxiBooking::where('driver_id', $driverId)
            ->whereBetween('pickup_date_time', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled'])
            ->count();

        $completedBookings = TaxiBooking::where('driver_id', $driverId)
            ->whereBetween('pickup_date_time', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        $totalDuration = TaxiBooking::where('driver_id', $driverId)
            ->whereBetween('pickup_date_time', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled'])
            ->sum('duration_minutes');

        return [
            'total_bookings' => $totalBookings,
            'completed_bookings' => $completedBookings,
            'completion_rate' => $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 2) : 0,
            'total_working_minutes' => $totalDuration,
            'total_working_hours' => round($totalDuration / 60, 2),
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString()
        ];
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
