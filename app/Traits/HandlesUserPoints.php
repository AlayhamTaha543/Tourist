<?php

namespace App\Traits;

use App\Models\PointRule;
use App\Models\Rank;
use App\Models\UserRank;

trait HandlesUserPoints
{
    public function addPointsFromAction($user, string $action, int $quantity = 1): void
    {
        $rule = PointRule::where('action', $action)->first();

        if (!$rule) {
            return; 
        }

        $points_to_add = $rule->points * $quantity;

        $user_rank = $user->rank ?? new UserRank(['user_id' => $user->id]);

        $user_rank->points_earned += $points_to_add;

        $user_rank->rank_id = Rank::where('min_points', '<=', $user_rank->points_earned)
            ->orderByDesc('min_points')
            ->first()?->id;

        $user_rank->save();
    }
}
