<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * Initialize service class.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return $this->resourceCollectionResponse(
            $this->userService->getAll()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        return $this->dbTransaction(function () use ($request) {
            $this->userService->create($request->validated());
            return $this->successResponse(
                __('action.created', ['type' => 'created']),
                JsonResponse::HTTP_ACCEPTED
            );
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return $this->resourceResponse(
            $this->userService->get($user)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest $request
     * @param  User $user
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, User $user)
    {
        return $this->dbTransaction(function () use ($request, $user) {
            $this->userService->update($user, $request->validated());
            return $this->successResponse(
                __('action.created', ['type' => 'updated']),
                JsonResponse::HTTP_ACCEPTED
            );
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        return $this->dbTransaction(function () use ($user) {
            $this->userService->delete($user);
            return $this->successResponse(
                __('action.created', ['type' => 'deleted']),
                JsonResponse::HTTP_ACCEPTED
            );
        });
    }
}
