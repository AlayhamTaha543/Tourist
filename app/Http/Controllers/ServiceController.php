<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\FeedBackRequest;
use App\Http\Requests\System\RatingRequest;
use App\Repositories\Interfaces\ServiceInterface;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $serviceRepository;
    public function __construct(ServiceInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function UserRank()
    {
        return $this->serviceRepository->UserRank();
    }
    public function discountPoints()
    {
        return $this->serviceRepository->discountPoints();
    }
    public function addRating(RatingRequest $request)
    {
        return $this->serviceRepository->addRating($request);
    }
    public function submitFeedback(FeedBackRequest $request)
    {
        return $this->serviceRepository->submitFeedback($request);
    }
    public function getAvailablePromotions()
    {
        return $this->serviceRepository->getAvailablePromotions();
    }
    public function requestTourAdmin(Request $request)
    {
        return $this->serviceRepository->requestTourAdmin($request);
    }
}
