<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedBack extends Model
{
    use HasFactory;
    
    protected $table = 'feedback'; 

    protected $fillable = [
        'user_id',
        'feedbackable_id',
        'feedbackable_type',
        'feedback_text',
        'feedback_date',
        'feedback_type',
        'status',
        'response_text',
        'response_date',
        'responded_by',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function feedbackable()
    {
        return $this->morphTo();
    }
}
