<?php

namespace App\Services\Admin;

use App\Http\Resources\AdminResource;
use App\Models\Admin;

class AuthenticationService
{
    /**
     * Authenticates the provided admin.
     *
     * @param Admin $admin
     * @return array<string, AdminResource|string>
     */
    public function authenticate(Admin $admin): array
    {
        $this->deleteTokens($admin);

        // Generate a new token.
        $token = $admin->createToken('access-token')->plainTextToken;

        return [
            'admin' => new AdminResource($admin),
            'token' => $token,
        ];
    }

    /**
     * Reauthenticates an admin.
     *
     * @return array
     */
    public function refreshToken(): array
    {
        return $this->authenticate(request()->user());
    }

    /**
     * Deletes all existing tokens of the admin specified.
     *
     * @param Admin $admin
     * @return void
     */
    public function deleteTokens(Admin $admin): void
    {
        // Revoke all tokens.
        $admin->tokens()->delete();
    }
}
