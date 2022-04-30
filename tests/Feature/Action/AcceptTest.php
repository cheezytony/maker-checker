<?php

namespace Tests\Feature\Action;

use App\Models\Action;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\UsesActions;
use Tests\UsesAdmin;
use Tests\UsesUsers;

class AcceptTest extends TestCase
{
    use RefreshDatabase;
    use UsesActions;
    use UsesAdmin;
    use UsesUsers;

    /**
     * Test the application returns a successful response for accepting a create action.
     *
     * @return void
     */
    public function testTheApplicationReturnsASuccessfulResponseForAccepttingACreateAction()
    {
        $this->generateAdmin();
        $this->generateUsers();
        $this->generateActions([
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'type' => Action::TYPE['CREATE'],
            'data' => $this->getParams(Action::TYPE['CREATE'])
        ]);
        // Overwrite with new authenticated admin.
        $this->generateAdmin(true);

        $this->putJson(
            route('actions.update', ['action' => $this->action['id']])
        )->assertCreated();

        $this->assertDatabaseHas('actions', [
            'id' => $this->action['id'],
            'authenticator_id' => $this->admin['id'],
            'type' => Action::TYPE['CREATE'],
            'status' => Action::STATUS['ACCEPTED'],
        ]);
    }

    /**
     * Test the application returns a successful response for accepting a delete action.
     *
     * @return void
     */
    public function testTheApplicationReturnsASuccessfulResponseForAccepttingADeleteAction()
    {
        $this->generateAdmin();
        $this->generateUsers();
        $this->generateActions([
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'type' => Action::TYPE['DELETE'],
            'data' => []
        ]);
        // Overwrite with new authenticated admin.
        $this->generateAdmin(true);

        $this->putJson(
            route('actions.update', ['action' => $this->action['id']])
        )->assertOk();

        $this->assertDatabaseHas('actions', [
            'id' => $this->action['id'],
            'authenticator_id' => $this->admin['id'],
            'type' => Action::TYPE['DELETE'],
            'status' => Action::STATUS['ACCEPTED'],
        ]);
    }

    /**
     * Test the application returns a successful response for accepting an update action.
     *
     * @return void
     */
    public function testTheApplicationReturnsASuccessfulResponseForAccepttingAnUpdateAction()
    {
        $this->generateAdmin();
        $this->generateUsers();
        $this->generateActions([
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'type' => Action::TYPE['UPDATE'],
            'data' => $this->getParams(Action::TYPE['UPDATE'])
        ]);
        // Overwrite with new authenticated admin.
        $this->generateAdmin(true);

        $this->putJson(
            route('actions.update', ['action' => $this->action['id']])
        )->assertOk();

        $this->assertDatabaseHas('actions', [
            'id' => $this->action['id'],
            'authenticator_id' => $this->admin['id'],
            'type' => Action::TYPE['UPDATE'],
            'status' => Action::STATUS['ACCEPTED'],
        ]);
    }

    /**
     * Test the application returns a bad request response for accepting an already processed action.
     *
     * @return void
     */
    public function testTheApplicationReturnsABadRequestResponseWhenAcceptingAProcessedAction()
    {
        $this->generateAdmin();
        $this->generateUsers();
        $this->generateActions([
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'type' => Action::TYPE['UPDATE'],
            'data' => $this->getParams(Action::TYPE['UPDATE']),
            'status' => Action::STATUS['ACCEPTED'],
        ]);

        // Overwrite with new authenticated admin.
        $this->generateAdmin(true);

        $this->putJson(
            route('actions.update', ['action' => $this->action['id']])
        )->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonPath('message', __('action.already-processed'));
    }

    /**
     * Test the application returns a bad request response when accepting an action from the same admin.
     *
     * @return void
     */
    public function testTheApplicationReturnsABadRequestResponseWhenIsSameAdmin()
    {
        $this->generateAdmin(true);
        $this->generateUsers();
        $this->generateActions([
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'type' => Action::TYPE['UPDATE'],
            'data' => $this->getParams(Action::TYPE['UPDATE']),
            'status' => Action::STATUS['PENDING'],
        ]);

        $this->putJson(
            route('actions.update', ['action' => $this->action['id']])
        )->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonPath('message', __('action.same-admin'));
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
