<?php

namespace App\Services\Admin;

use App\Models\Admin;

class RegisterService
{
    /**
     * @var AuthenticationService
     */
    protected AuthenticationService $authenticationService;

    /**
     * Initialize service class.
     *
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * Creates an account for a new admin.
     *
     * @param array $params
     * @return array<string, string>
     */
    public function createAccount(array $params): array
    {
        return $this->authenticationService->authenticate(
            Admin::create($params)
        );
    }
}
