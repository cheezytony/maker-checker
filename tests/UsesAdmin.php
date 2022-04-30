<?php

namespace Tests;

use App\Models\Admin;
use Laravel\Sanctum\Sanctum;

trait UsesAdmin
{
    /**
     * Admin used for authorizing tests
     *
     * @var Admin
     */
    protected Admin $admin;

    /**
     * Generates an admin for testing
     *
     * @return void
     */
    protected function generateAdmin(bool $authorizeRequests = false): void
    {
        $this->admin = Admin::factory()->create();

        if ($authorizeRequests) {
            Sanctum::actingAs($this->admin);
        }
    }
}
