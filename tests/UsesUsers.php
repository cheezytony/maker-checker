<?php

namespace Tests;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait UsesUsers
{
    /**
     * Single user instance to be used for testing.
     *
     * @var User
     */
    protected User $user;

    /**
     * User instances to be used for testing.
     *
     * @var Collection
     */
    protected Collection $users;

    /**
     * Generates user instances for testing.
     *
     * @return void
     */
    protected function generateUsers(): void
    {
        $this->users = User::factory(5)->create();
        $this->user = $this->users[0];
    }
}
