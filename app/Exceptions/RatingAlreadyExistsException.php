<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingAlreadyExistsException extends Exception
{
    protected $code = 409;
    protected $message = 'A rating already exists for this booking and driver';

    public function __construct(
        string $message = null,
        int $code = null,
        Exception $previous = null
    ) {
        parent::__construct(
            $message ?? $this->message,
            $code ?? $this->code,
            $previous
        );
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => class_basename($this),
            'message' => $this->getMessage(),
        ], $this->getCode());
    }

    public function report(): void
    {

    }
}
