<?php

namespace App\Traits;

use App\Models\PointRule;
use App\Models\Rank;
use App\Models\UserRank;

trait HandlesUserPoints
{
    public function addPointsFromAction($user, float $totalPrice, float $discountAmount = 0): float
    {
        $calculatedPoints = $totalPrice - $discountAmount;
        $points_to_add = min($calculatedPoints, 20000); // Apply 20,000 point cap

        if ($points_to_add <= 0) {
            return 0.0;
        }

        $userRank = $user->userRank ?? new UserRank(['user_id' => $user->id]);

        $userRank->points_earned += $points_to_add;

        $userRank->rank_id = Rank::where('min_points', '<=', $userRank->points_earned)
            ->orderByDesc('min_points')
            ->first()?->id;

        $userRank->save();

        return $points_to_add;
    }
}
