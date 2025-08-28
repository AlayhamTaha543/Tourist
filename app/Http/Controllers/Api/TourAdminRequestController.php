<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourAdminRequest\StoreTourAdminRequest;
use App\Models\TourAdminRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;
use App\Filament\SuperAdmin\Notifications\NewTourAdminRequestNotification;
use Illuminate\Http\JsonResponse;

class TourAdminRequestController extends Controller
{
    public function store(StoreTourAdminRequest $request): JsonResponse
    {
        $personalImagePath = $request->file('personal_image')->store('tour_admin_requests/personal_images', 'public');
        $certificateImagePath = $request->file('certificate_image')->store('tour_admin_requests/certificate_images', 'public');

        $tourAdminRequest = TourAdminRequest::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'age' => $request->age,
            'skills' => $request->skills,
            'personal_image' => $personalImagePath,
            'certificate_image' => $certificateImagePath,
            'status' => 'pending',
        ]);

        $superAdmins = Admin::where('role', 'super_admin')->get();
        foreach ($superAdmins as $superAdmin) {
            $superAdmin->notify(new NewTourAdminRequestNotification($tourAdminRequest));
        }

        return response()->json([
            'message' => 'Tour admin request submitted successfully. Awaiting super admin approval.',
            'data' => $tourAdminRequest
        ], 201);
    }
}