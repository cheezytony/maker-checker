<?php

namespace App\Services;

use App\Events\Action\ActionCreatedEvent;
use App\Exceptions\JsonException;
use App\Http\Resources\ActionResource;
use App\Http\Resources\ActionResourceCollection;
use App\Http\Resources\UserResource;
use App\Models\Action;
use App\Models\User;
use Illuminate\Http\Response;

class ActionService
{
    /**
     * Get all actions
     *
     * @return ActionResourceCollection
     */
    public function getAll(): ActionResourceCollection
    {
        return new ActionResourceCollection(
            Action::filter()->paginate()
        );
    }

    /**
     * Wraps and returns an action in a resource.
     *
     * @param Action $action
     * @return ActionResource
     */
    public function get(Action $action): ActionResource
    {
        return new ActionResource($action);
    }

    /**
     * Registeres a new pending action and notifies other admins.
     *
     * @param string $type
     * @param array $data
     * @param ?User $user
     * @return ActionResource
     */
    public function create(string $type, array $data = [], User $user = null): ActionResource
    {
        $action = Action::create([
            'admin_id' => auth()->id(),
            'user_id' => optional($user)->id,
            'type' => $type,
            'data' => $data,
        ]);

        event(new ActionCreatedEvent($action));

        return new ActionResource($action);
    }

    /**
     * Get all actions
     *
     * @param Action $action
     * @return ActionResource|UserResource|null
     */
    public function accept(Action $action): ActionResource|UserResource|null
    {
        /**
         * @var UserService
         */
        $userService = app(UserService::class);
        $data = null;

        $this->abortIfIsSameAdmin($action);
        $this->abortIfNotPending($action);

        switch ($action['type']) {
            case Action::TYPE['CREATE']:
                $data = $userService->create((array) $action->data, true);
                break;
            case Action::TYPE['DELETE']:
                $userService->delete($action->user, true);
                break;
            case Action::TYPE['UPDATE']:
                $data = $userService->update($action->user, (array) $action->data, true);
                break;

            default:
                throw new JsonException(
                    __('action.unknown-type'),
                    Response::HTTP_BAD_REQUEST
                );
        }

        $action->update([
            'status' => Action::STATUS['ACCEPTED'],
            'authenticator_id' => auth()->id(),
        ]);

        return $data;
    }

    /**
     * Get all actions
     *
     * @return ActionResource
     */
    public function reject(Action $action): ActionResource
    {
        $this->abortIfNotPending($action);

        $action->update([
            'status' => Action::STATUS['REJECTED'],
            'authenticator_id' => auth()->id(),
        ]);

        return new ActionResource($action);
    }

    /**
     * Throws an exception if the specified action's status is not pending.
     *
     * @param Action $action
     * @return void
     */
    public function abortIfNotPending(Action $action): void
    {
        if ($action['status'] !== Action::STATUS['PENDING']) {
            throw new JsonException(
                __('action.already-processed'),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Throws an exception if an admin tries to approved their own action.
     *
     * @param Action $action
     * @return void
     */
    public function abortIfIsSameAdmin(Action $action): void
    {
        if ($action['admin_id'] === auth()->id()) {
            throw new JsonException(
                __('action.same-admin'),
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
