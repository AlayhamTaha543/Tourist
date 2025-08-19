<?php

namespace App\Helper;

use Laravel\Sanctum\PersonalAccessToken;

class AuthHelper
{
    private static $authenticatedUser = null;
    private static $isAuthenticated = false;
    private static $initialized = false;

    /**
     * Initialize authentication status from request
     */
    public static function init($request = null)
    {
        if (self::$initialized) {
            return;
        }

        if (!$request) {
            $request = request();
        }

        $accessToken = $request->bearerToken();
        
        // Alternative token extraction
        if (!$accessToken) {
            $authHeader = $request->header('Authorization');
            if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
                $accessToken = substr($authHeader, 7);
            }
        }

        if ($accessToken) {
            $user = self::getUserFromToken($accessToken);
            if ($user) {
                self::$authenticatedUser = $user;
                self::$isAuthenticated = true;
            }
        }

        self::$initialized = true;
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(): bool
    {
        if (!self::$initialized) {
            self::init();
        }
        
        return self::$isAuthenticated;
    }

    /**
     * Get authenticated user
     */
    public static function user()
    {
        if (!self::$initialized) {
            self::init();
        }
        
        return self::$authenticatedUser;
    }

    /**
     * Reset authentication state (useful for testing)
     */
    public static function reset()
    {
        self::$authenticatedUser = null;
        self::$isAuthenticated = false;
        self::$initialized = false;
    }

    /**
     * Get user from Sanctum token
     */
    private static function getUserFromToken(string $accessToken)
    {
        try {
            $tokenModel = PersonalAccessToken::findToken($accessToken);
            
            if (!$tokenModel) {
                return null;
            }
            
            if ($tokenModel->expires_at && $tokenModel->expires_at->isPast()) {
                return null;
            }
            
            return $tokenModel->tokenable;
            
        } catch (\Exception $e) {
            \Log::error('Error retrieving user from token: ' . $e->getMessage());
            return null;
        }
    }
}