<?php

namespace App\Traits;

use App\Models\LeaveRequest;
use App\Models\TravelPermission;
use Illuminate\Support\Collection;

/**
 * A staff member can hold at most one position at a time - whether they're
 * covering someone's Leave or someone's Travel, an active hold in either
 * table makes them unavailable for a new one in both.
 */
trait ActingStaffAvailabilityTrait
{
    /**
     * IDs of staff currently accepted to hold a position (Leave or Travel)
     * that overlaps the given date range.
     */
    protected function busyHolderIds(string $startDate, string $endDate): Collection
    {
        $fromLeave = LeaveRequest::where('acting_status', 'accepted')
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->pluck('acting_staff_id');

        $fromTravel = TravelPermission::where('acting_status', 'accepted')
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->pluck('acting_staff_id');

        return $fromLeave->merge($fromTravel)->unique();
    }

    /**
     * Whether the given staff member is already accepted to hold a
     * different position (Leave or Travel) overlapping the given dates.
     */
    protected function isAlreadyHoldingPosition($staffId, string $startDate, string $endDate): bool
    {
        return $this->busyHolderIds($startDate, $endDate)->contains($staffId);
    }
}
