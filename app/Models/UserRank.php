<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRank extends Model
{
    protected $table = 'user_ranks';
    protected $fillable = ['user_id', 'rank_id', 'points_earned'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }
}
