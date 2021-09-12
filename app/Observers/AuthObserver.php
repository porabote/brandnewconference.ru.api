<?php

namespace App\Observers;

use Porabote\Auth\Auth as PrbAuth;


class AuthObserver
{
    /**
     * Handle the Auth "created" event.
     *
     * @param  \App\Models\Auth  $auth
     * @return void
     */
    public function creating($model)
    {
       // debug($model);
        $model->user_id = PrbAuth::getUser('id');
        $model->user_name = PrbAuth::getUser('name');
    }

    /**
     * Handle the Auth "updated" event.
     *
     * @param  \App\Models\Auth  $auth
     * @return void
     */
    public function updated(Auth $auth)
    {
        //
    }

    /**
     * Handle the Auth "deleted" event.
     *
     * @param  \App\Models\Auth  $auth
     * @return void
     */
    public function deleted(Auth $auth)
    {
        //
    }

    /**
     * Handle the Auth "restored" event.
     *
     * @param  \App\Models\Auth  $auth
     * @return void
     */
    public function restored(Auth $auth)
    {
        //
    }

    /**
     * Handle the Auth "force deleted" event.
     *
     * @param  \App\Models\Auth  $auth
     * @return void
     */
    public function forceDeleted(Auth $auth)
    {
        //
    }
}
