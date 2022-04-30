<?php

namespace Tests;

use App\Models\Action;
use Illuminate\Database\Eloquent\Collection;

trait UsesActions
{
    /**
     * Single action instance to be used for testing.
     *
     * @var Action
     */
    protected Action $action;

    /**
     * Action instances to be used for testing.
     *
     * @var Collection
     */
    protected Collection $actions;

    /**
     * Generates action instances for testing.
     *
     * @return void
     */
    protected function generateActions(array $params = []): void
    {
        $this->actions = Action::factory(5)->create($params);
        $this->action = $this->actions[0];
    }
}
