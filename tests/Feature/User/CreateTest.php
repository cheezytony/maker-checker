<?php

namespace Tests\Feature\User;

use App\Models\Action;
use App\Models\Admin;
use App\Notifications\Action\ActionCreatedNotification;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\UsesAdmin;
use Tests\UsesUsers;

class CreateTest extends TestCase
{
    use RefreshDatabase;
    use UsesAdmin;
    use UsesUsers;

    /**
     * Test the application returns a successful response with correct parameters.
     *
     * @return void
     */
    public function testTheApplicationReturnsAnAcceptedResponseWithCorrectParameters()
    {
        Notification::fake();

        // Populate admins table with other admins to be notified.
        $this->seed(AdminSeeder::class);
        $this->generateAdmin(true);

        $params = $this->getParams();
        $response = $this->postJson(route('users.store'), $params);

        $response->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonPath('message', __('action.created', ['type' => 'created']));

        $this->assertDatabaseHas('actions', [
            'admin_id' => $this->admin['id'],
            'user_id' => null,
            'authenticator_id' => null,
            'data' => $this->castAsJson($params),
            'type' => Action::TYPE['CREATE'],
            'status' => Action::STATUS['PENDING'],
        ]);

        Admin::where('id', '!=', $this->admin['id'])->get()
            ->each(function (Admin $a) {
                Notification::assertSentTo($a, ActionCreatedNotification::class);
            });
    }

    /**
     * Test the application returns unprocessable with empty credentials.
     *
     * @return void
     */
    public function testTheApplicationReturnsUnprocessableWithEmptyCredentials()
    {
        $this->generateAdmin(true);

        $response = $this->postJson(route('users.store'), []);
        $response->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        __('validation.required', ['attribute' => 'first name'])
                    ],
                    'last_name' => [
                        __('validation.required', ['attribute' => 'last name'])
                    ],
                    'email' => [
                        __('validation.required', ['attribute' => 'email'])
                    ],
                ],
            ]);
    }

    /**
     * Test the application returns unprocessable with an invalid email.
     *
     * @return void
     */
    public function testTheApplicationReturnsUnprocessableWithAnInvalidEmail()
    {
        $this->generateAdmin(true);

        $response = $this->postJson(route('users.store'), $this->getParams('invalid-email'));
        $response->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'email' => [
                        __('validation.email', ['attribute' => 'email'])
                    ],
                ],
            ]);
    }

    /**
     * Test the application returns unprocessable with an existing email
     *
     * @return void
     */
    public function testTheApplicationReturnsUnprocessableWithAnExistingEmail()
    {
        $this->generateAdmin(true);
        $this->generateUsers();

        $response = $this->postJson(route('users.store'), $this->getParams('existing-email'));
        $response->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'email' => [
                        __('validation.unique', ['attribute' => 'email'])
                    ],
                ],
            ]);
    }

    /**
     * Generates request parameters
     *
     * @param string $paramsValidity
     * @return array
     */
    public function getParams(string $paramsValidity = ''): array
    {
        return match ($paramsValidity) {
            'invalid-email' => [
                'email' => 'invalid-email',
                'password' => 'password',
                'last_name' => 'Okoro',
                'first_name' => 'Antonio',
            ],
            'existing-email' => [
                'email' => $this->user['email'],
                'password' => 'password',
                'last_name' => 'Okoro',
                'first_name' => 'Antonio',
            ],
            default => [
                'email' => 'email@example.com',
                'last_name' => 'Okoro',
                'first_name' => 'Antonio',
            ],
        };
    }
}
