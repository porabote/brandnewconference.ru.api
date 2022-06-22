<?php

namespace App\Observers;

use Porabote\Auth\Auth;


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
        $attrs = $model->getOriginal();

        $model->user_id = Auth::getUser('id');

        if (array_key_exists('user_name', $attrs)) $model->user_name = Auth::getUser('name');

        if (array_key_exists('date_created', $attrs)) $model->date_created = date("Y-m-d H:i:s");

    }

    /**
     * Handle the Auth "updated" event.
     *
     * @param  \App\Models\Auth  $auth
     * @return void
     */
//    public function updated(Auth $auth)
//    {
//        //
//    }

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
