<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class JsonException extends Exception
{
    /**
     * Render the exception as json.
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return new JsonResponse(
            ['message' => $this->getMessage()],
            $this->getCode()
        );
    }
}
