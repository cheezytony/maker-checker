<?php

namespace Tests\Feature\Admin;

use App\Models\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\UsesAdmin;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    use UsesAdmin;

    /**
     * Test that login returns successful with correct credentials.
     *
     * @return void
     */
    public function testThatLoginReturnsSuccessfulWithCorrectCredentials()
    {
        $this->generateAdmin();

        $response = $this->postJson(route('login'), $this->getParams());
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'admin' => $this->admin->toArray(),
                    'token' => true,
                ],
            ]);

        $this->assertDatabaseHas('logins', [
            'admin_id' => $this->admin['id'],
            'status' => Login::STATUS['AUTHENTICATED'],
        ]);
    }

    /**
     * Test that login returns unprocessable with empty credentials.
     *
     * @return void
     */
    public function testThatLoginReturnsUnprocessableWithEmptyCredentials()
    {
        $response = $this->postJson(route('login'), []);
        $response->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'email' => [
                        __('validation.required', ['attribute' => 'email'])
                    ],
                    'password' => [
                        __('validation.required', ['attribute' => 'password'])
                    ],
                ],
            ]);
    }

    /**
     * Test that login returns unprocessable with an invalid email.
     *
     * @return void
     */
    public function testThatLoginReturnsUnprocessableWithAnInvalidEmail()
    {
        $response = $this->postJson(route('login'), $this->getParams('invalid-email'));
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
     * Test that login returns unprocessable with a wrong email.
     *
     * @return void
     */
    public function testThatLoginReturnsUnprocessableWithAWrongEmail()
    {
        $response = $this->postJson(route('login'), $this->getParams('wrong-email'));
        $response->assertUnauthorized();
    }

    /**
     * Test that login returns unprocessable with a wrong password.
     *
     * @return void
     */
    public function testThatLoginReturnsUnprocessableWithAWrongPassword()
    {
        $this->generateAdmin();

        $response = $this->postJson(route('login'), $this->getParams('wrong-password'));
        $response->assertUnauthorized();

        $this->assertDatabaseHas('logins', [
            'admin_id' => $this->admin['id'],
            'status' => Login::STATUS['UNAUTHENTICATED'],
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
            ],
            'wrong-email' => [
                'email' => 'wrong-email@gmail.com',
                'password' => 'password',
            ],
            'wrong-password' => [
                'email' => $this->admin['email'],
                'password' => 'wrong-password'
            ],
            default => [
                'email' => $this->admin['email'],
                'password' => 'password'
            ],
        };
    }
}
