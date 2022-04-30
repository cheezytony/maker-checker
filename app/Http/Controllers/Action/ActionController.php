<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Services\ActionService;
use Illuminate\Http\JsonResponse;

class ActionController extends Controller
{
    /**
     * @var ActionService
     */
    protected ActionService $actionService;

    /**
     * Initialize service class.
     *
     * @param ActionService $actionService
     */
    public function __construct(ActionService $actionService)
    {
        $this->actionService = $actionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->resourceCollectionResponse(
            $this->actionService->getAll()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  Action  $action
     * @return JsonResponse
     */
    public function show(Action $action): JsonResponse
    {
        return $this->resourceResponse(
            $this->actionService->get($action)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Action  $action
     * @return JsonResponse
     */
    public function update(Action $action): JsonResponse
    {
        return $this->dbTransaction(function () use ($action) {
            $response = $this->actionService->accept($action);
            switch ($action['type']) {
                case Action::TYPE['DELETE']:
                    return $this->successResponse();
                case Action::TYPE['UPDATE']:
                    return $this->resourceResponse($response);
                case Action::TYPE['CREATE']:
                default:
                    return $this->resourceResponse($response, JsonResponse::HTTP_CREATED);
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Action  $action
     * @return JsonResponse
     */
    public function destroy(Action $action): JsonResponse
    {
        $this->actionService->reject($action);
        return $this->successResponse(__('action.rejected'));
    }
}
