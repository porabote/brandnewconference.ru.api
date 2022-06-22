<?php

namespace App\Observers;

use App\Models\History;
use Porabote\Auth\Auth;
use Carbon\Carbon;

class HistoryObserver
{
    public function creating($model)
    {
        $className = (new \ReflectionClass($model))->getShortName();

        $attrs = $model->getOriginal();
        if (array_key_exists('user_id', $attrs)) $model->user_id = Auth::getUser('id');
        if (array_key_exists('user_name', $attrs)) $model->user_name = Auth::getUser('name');
        if (array_key_exists('date_created', $attrs)) $model->date_created = Carbon::now();

    }
    /**
     * Handle the History "created" event.
     *
     * @param  \App\Models\History  $record
     * @return void
     */
    public function created($record)
    {
        $className = (new \ReflectionClass($record))->getShortName();
        History::create([
            'model_alias' => $className,
            'record_id' => $record->toArray()['id'],
            'msg' => 'Добавлена новая запись'
        ]);
    }

    /**
     * Handle the History "updated" event.
     *
     * @param  \App\Models\History  $history
     * @return void
     */
    public function updated($record)
    {
        $className = (new \ReflectionClass($record))->getShortName();
        History::create([
            'model_alias' => $className,
            'record_id' => $record->toArray()['id'],
            'msg' => 'Обновлены данные записи'
        ]);
    }

    /**
     * Handle the History "deleted" event.
     *
     * @param  \App\Models\History  $history
     * @return void
     */
    public function deleted(History $history)
    {
        $className = (new \ReflectionClass($record))->getShortName();
        History::create([
            'model_alias' => $className,
            'record_id' => $record->toArray()['id'],
            'msg' => 'Запись удалена'
        ]);
    }

    /**
     * Handle the History "restored" event.
     *
     * @param  \App\Models\History  $history
     * @return void
     */
    public function restored(History $history)
    {
        //
    }

    /**
     * Handle the History "force deleted" event.
     *
     * @param  \App\Models\History  $history
     * @return void
     */
    public function forceDeleted(History $history)
    {
        //
    }
}
