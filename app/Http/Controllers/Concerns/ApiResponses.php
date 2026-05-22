<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

/**
 * A consistent JSON envelope for the v1 API: { data, message, meta }.
 * Keeps every endpoint shaped the same so SDK/clients can rely on it.
 */
trait ApiResponses
{
    protected function ok(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'data'    => $data,
            'message' => $message,
        ], $status);
    }

    protected function created(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return $this->ok($data, $message, 201);
    }

    protected function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        return response()->json(array_filter([
            'message' => $message,
            'errors'  => $errors ?: null,
        ]), $status);
    }
}
