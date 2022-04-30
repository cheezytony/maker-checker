<?php

namespace App\Services;

use App\Http\Resources\ActionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Models\Action;
use App\Models\User;
use App\Services\ActionService as ServicesActionService;

class UserService
{
    /**
     * @var ServicesActionService
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
     * Paginates all users
     *
     * @return UserResourceCollection
     */
    public function getAll(): UserResourceCollection
    {
        return new UserResourceCollection(User::paginate());
    }

    /**
     * Wraps and returns a user in a resource.
     *
     * @param User $user
     * @return UserResource
     */
    public function get(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Creates a new user.
     *
     * @param array<string, string> $params
     * @param bool $isAccepted
     * @return ActionResource|UserResource
     */
    public function create(array $params, bool $isAccepted = false): ActionResource|UserResource
    {
        if (!$isAccepted) {
            return $this->actionService->create(Action::TYPE['CREATE'], $params);
        }

        return new UserResource(
            User::create($params)
        );
    }

    /**
     * Deletes the specified user.
     *
     * @param User $user
     * @param bool $isAccepted
     * @return ActionResource|bool|null
     */
    public function delete(User $user, bool $isAccepted = false): ActionResource|bool|null
    {
        if (!$isAccepted) {
            return $this->actionService->create(Action::TYPE['DELETE'], [], $user);
        }

        return $user->delete();
    }

    /**
     * Updates the specified user with the params provided.
     *
     * @param User $user
     * @param array<string, string> $params
     * @param bool $isAccepted
     * @return ActionResource|UserResource
     */
    public function update(User $user, array $params, bool $isAccepted = false): ActionResource|UserResource
    {
        if (!$isAccepted) {
            return $this->actionService->create(Action::TYPE['UPDATE'], $params, $user);
        }

        $user->update($params);
        $user->refresh();

        return new UserResource($user);
    }
}
