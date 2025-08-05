<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\System\FeedBackRequest;
use App\Http\Requests\System\RatingRequest;
use Illuminate\Http\Request;

interface ServiceInterface
{
    public function UserRank();
    public function discountPoints();
    public function addRating(RatingRequest $request);
    public function submitFeedback(FeedBackRequest $request);
    public function getAvailablePromotions();
    public function requestTourAdmin(Request $request);
}
