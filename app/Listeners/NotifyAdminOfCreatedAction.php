<?php

namespace App\Listeners;

use App\Events\Action\ActionCreatedEvent;
use App\Models\Action;
use App\Models\Admin;
use App\Notifications\Action\ActionCreatedNotification;

class NotifyAdminOfCreatedAction
{
    /**
     * Handle the event.
     *
     * @param  ActionCreatedEvent $event
     * @return void
     */
    public function handle(ActionCreatedEvent $event)
    {
        /**
         * @var Action
         */
        $action = $event->action;
        $admin = $action->admin;
        $admins = Admin::where('id', '!=', $admin->id)->get();

        // Could be replaced with chunk if query would return a lot of data
        $admins->each(function (Admin $admin) use ($action) {
            $admin->notify(new ActionCreatedNotification($action));
        });
    }
}
