<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\UsesAdmin;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use UsesAdmin;

    /**
     * Test that register returns created with correct parameters.
     *
     * @return void
     */
    public function testThatRegisterReturnsCreatedWithCorrectParameters()
    {
        $response = $this->postJson(route('register'), $this->getParams());

        $response->assertCreated()
            ->assertJson([
                'data' => [
                    'admin' => [
                        'email' => 'test-email@' . config('app.domain'),
                    ],
                    'token' => true,
                ],
            ]);

        $this->assertDatabaseHas('admins', [
            'email' => 'test-email@' . config('app.domain')
        ]);
    }

    /**
     * Test that register returns unprocessable with empty parameters.
     *
     * @return void
     */
    public function testThatRegisterReturnsUnprocessableWithEmptyParameters()
    {
        $response = $this->postJson(route('register'), []);

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
     * Test that register returns unprocessable with an invalid email.
     *
     * @return void
     */
    public function testThatRegisterReturnsUnprocessableWithAnInvalidEmail()
    {
        $response = $this->postJson(route('register'), $this->getParams('invalid-email'));

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
     * Test that register returns unprocessable with a public email.
     *
     * @return void
     */
    public function testThatRegisterReturnsUnprocessableWithAPublicEmail()
    {
        $response = $this->postJson(route('register'), $this->getParams('public-email'));

        $response->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'email' => [
                        __('validation.custom.company_email', ['attribute' => 'email'])
                    ],
                ],
            ]);
    }


    /**
     * Test that register returns unprocessable with an existing email.
     *
     * @return void
     */
    public function testThatRegisterReturnsUnprocessableWithAnExistingEmail()
    {
        $this->generateAdmin();

        $response = $this->postJson(route('register'), $this->getParams('existing-email'));

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
            ],
            'public-email' => [
                'email' => 'email@gmail.com',
                'password' => 'password',
            ],
            'existing-email' => [
                'email' => $this->admin['email'],
                'password' => 'password',
            ],
            default => [
                'email' => 'test-email@' . config('app.domain'),
                'password' => 'password'
            ],
        };
    }
}
