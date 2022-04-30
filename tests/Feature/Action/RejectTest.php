<?php

namespace Tests\Feature\Action;

use App\Models\Action;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\UsesActions;
use Tests\UsesAdmin;
use Tests\UsesUsers;

class RejectTest extends TestCase
{
    use RefreshDatabase;
    use UsesActions;
    use UsesAdmin;
    use UsesUsers;

    /**
     * Test the application returns a successful response for rejecting a create action.
     *
     * @return void
     */
    public function testTheApplicationReturnsASuccessfulResponseForRejectingACreateAction()
    {
        $this->generateAdmin(true);
        $this->generateUsers();
        $this->generateActions([
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'type' => Action::TYPE['CREATE'],
            'data' => $this->getParams(Action::TYPE['CREATE'])
        ]);

        $this->deleteJson(
            route('actions.destroy', ['action' => $this->action['id']])
        )->assertOk();

        $this->assertDatabaseHas('actions', [
            'id' => $this->action['id'],
            'authenticator_id' => $this->admin['id'],
            'type' => Action::TYPE['CREATE'],
            'status' => Action::STATUS['REJECTED'],
        ]);
    }

    /**
     * Test the application returns a successful response for rejecting a delete action.
     *
     * @return void
     */
    public function testTheApplicationReturnsASuccessfulResponseForRejectingADeleteAction()
    {
        $this->generateAdmin(true);
        $this->generateUsers();
        $this->generateActions([
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'type' => Action::TYPE['DELETE'],
            'data' => []
        ]);

        $this->deleteJson(
            route('actions.destroy', ['action' => $this->action['id']])
        )->assertOk();

        $this->assertDatabaseHas('actions', [
            'id' => $this->action['id'],
            'authenticator_id' => $this->admin['id'],
            'type' => Action::TYPE['DELETE'],
            'status' => Action::STATUS['REJECTED'],
        ]);
    }

    /**
     * Test the application returns a successful response for rejecting an update action.
     *
     * @return void
     */
    public function testTheApplicationReturnsASuccessfulResponseForRejectingAnUpdateAction()
    {
        $this->generateAdmin(true);
        $this->generateUsers();
        $this->generateActions([
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'type' => Action::TYPE['UPDATE'],
            'data' => $this->getParams(Action::TYPE['UPDATE'])
        ]);

        $this->deleteJson(
            route('actions.destroy', ['action' => $this->action['id']])
        )->assertOk();

        $this->assertDatabaseHas('actions', [
            'id' => $this->action['id'],
            'authenticator_id' => $this->admin['id'],
            'type' => Action::TYPE['UPDATE'],
            'status' => Action::STATUS['REJECTED'],
        ]);
    }

    /**
     * Test the application returns a bad request response for rejecting an already processed action.
     *
     * @return void
     */
    public function testTheApplicationReturnsABadRequestResponseWhenRejectingAnAlreadyProcessedAction()
    {
        $this->generateAdmin(true);
        $this->generateUsers();
        $this->generateActions([
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'type' => Action::TYPE['UPDATE'],
            'data' => $this->getParams(Action::TYPE['UPDATE']),
            'status' => Action::STATUS['ACCEPTED'],
        ]);

        $this->deleteJson(
            route('actions.destroy', ['action' => $this->action['id']])
        )->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonPath('message', __('action.already-processed'));
    }

    /**
     * Generates request parameters
     *
     * @param string $actionType
     * @return array
     */
    public function getParams(string $actionType): array
    {
        return match ($actionType) {
            Action::TYPE['CREATE'] => [
                'email' => 'new-email@example.com',
                'last_name' => 'Okoro',
                'first_name' => 'Antonio',
            ],
            Action::TYPE['UPDATE'] => [
                'email' => $this->user['email'],
                'password' => 'password',
                'last_name' => 'Okoro',
                'first_name' => 'Antonio',
            ],
            default => [],
        };
    }
}
