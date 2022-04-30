<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Models\Login;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class LoginService
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
     * Attempts to authenticate an admin
     *
     * @param string $email
     * @param string $password
     * @throws AuthenticationException
     * @return array<string, string>
     */
    public function login(string $email, string $password): array
    {
        $admin = Admin::whereEmail($email)->first();
        if (!$admin) {
            throw new AuthenticationException();
        }

        if (!Hash::check($password, $admin->password)) {
            // Admin could be notified of suspicious authentication attempt.
            $this->recordLogin($admin, Login::STATUS['UNAUTHENTICATED']);
            throw new AuthenticationException();
        }

        $this->recordLogin($admin, Login::STATUS['AUTHENTICATED']);

        return $this->authenticationService->authenticate($admin);
    }

    /**
     * Records a login attempt.
     *
     * @param Admin $admin
     * @param string $status
     * @return void
     */
    public function recordLogin(Admin $admin, string $status): void
    {
        Login::create([
            'admin_id' => $admin->id,
            'status' => $status
        ]);
    }
}
