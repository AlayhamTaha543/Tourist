<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedBack extends Model
{
    
    protected $table = 'feedback'; 

    protected $fillable = [
        'user_id',
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
}
