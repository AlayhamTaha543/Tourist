<?php

namespace App\Traits;

trait ApiResponse
{
    protected function ok($message, $statusCode = 200)
    {
        return $this->success($message, null, $statusCode);
    }

    protected function success($message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }

    protected function error($message, $statusCode)
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
