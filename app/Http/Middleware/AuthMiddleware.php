<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();

        if (! $user) {
            abort(403); 
        }

        $panelMap = [
            'restaurant' => [
                'admin' => 'restaurantAdmin',
                'sub_admin' => 'restaurantSubAdmin',
            ],
            'hotel' => [
                'admin' => 'hotelAdmin',
                'sub_admin' => 'hotelSubAdmin',
            ],
            'tour' => [
                'admin' => 'tourAdmin',
                'sub_admin' => 'tourSubAdmin',
            ],
            'travel' => [
                'admin' => 'travelAdmin',
                'sub_admin' => 'travelSubAdmin',
            ],
        ];

        $role = $user->role;
        $section = $user->section;

        if (isset($panelMap[$section][$role])) {
            return redirect()->to(Filament::getPanel($panelMap[$section][$role])->getUrl());
        }

        if ($role === 'admin') {
            return redirect()->to(Filament::getPanel('admin')->getUrl());
        }

        return $next($request); 
    
    }
}
