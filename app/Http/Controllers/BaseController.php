<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    protected function resourceNotFound(string $resource = 'Resource'): JsonResponse
    {
        return $this->errorResponse("$resource not found", 404);
    }

    protected function handleException(\Exception $e): JsonResponse
    {

        $message = config('app.debug') ? $e->getMessage() : 'Server Error';
        return $this->errorResponse($message, 500);
    }
}
