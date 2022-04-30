<?php

namespace App\Traits;

use App\Services\PaginationService;
use Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

trait ReturnsJsonResponses
{
    public function jsonResponse(
        array $data,
        int $status = Response::HTTP_OK,
        string $message = null
    ): JsonResponse {
        return new JsonResponse([
            'data' => $data,
            'message' => $message ?: __('response.success')
        ], $status);
    }
    /**
     * Returns a json response with a resource collection.
     * Possibly paginated.
     *
     * @param ResourceCollection $collection
     * @param int $status
     * @return JsonResponse
     */
    public function resourceCollectionResponse(
        ResourceCollection $collection,
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return new JsonResponse($collection, $status);
    }
    
    /**
     * Returns a json response with a model resource.
     *
     * @param JsonResource $resource
     * @param int $status
     * @param string|null $message
     * @return JsonResponse
     */
    public function resourceResponse(
        JsonResource $resource,
        int $status = Response::HTTP_OK,
        string $message = null
    ): JsonResponse {
        $data = [
            'data' => $resource,
            'message' => $message ?: __('response.success')
        ];
        return new JsonResponse($data, $status);
    }

    /**
     * Returns a successful json response
     *
     * @param string|null $message
     * @param int $status
     * @return JsonResponse
     */
    public function successResponse(
        string $message = null,
        int $status = Response::HTTP_OK
    ): JsonResponse {
        $data = [
            'success' => true,
            'message' => $message ?: __('response.success')
        ];
        return new JsonResponse($data, $status);
    }
}
