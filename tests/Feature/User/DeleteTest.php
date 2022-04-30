<?php

namespace Tests\Feature\User;

use App\Models\Action;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\Action\ActionCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\UsesAdmin;
use Tests\UsesUsers;

class DeleteTest extends TestCase
{
    use RefreshDatabase;
    use UsesAdmin;
    use UsesUsers;

    /**
     * Test the application returns accepted response.
     *
     * @return void
     */
    public function testTheApplicationReturnsAcceptedResponse()
    {
        Notification::fake();

        $this->generateAdmin(true);
        $this->generateUsers();

        $response = $this->deleteJson(
            route('users.destroy', ['user' => $this->user['id']])
        );

        $response->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonPath('message', __('action.created', ['type' => 'deleted']));

        $this->assertDatabaseHas('actions', [
            'admin_id' => $this->admin['id'],
            'user_id' => $this->user['id'],
            'authenticator_id' => null,
            'data' => $this->castAsJson([]),
            'type' => Action::TYPE['DELETE'],
            'status' => Action::STATUS['PENDING'],
        ]);


        Admin::where('id', '!=', $this->admin['id'])->get()
            ->each(function (Admin $a) {
                Notification::assertSentTo($a, ActionCreatedNotification::class);
            });
    }

    /**
     * Test the application returns not found with a wrong user id.
     *
     * @return void
     */
    public function testTheApplicationReturnsNotFoundWithAWrongUserId()
    {
        $this->generateAdmin(true);

        $response = $this->deleteJson(
            route('users.destroy', ['user' => 1])
        );

        $response->assertNotFound();
    }
}
