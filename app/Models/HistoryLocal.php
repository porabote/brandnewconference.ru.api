<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Observers\AuthObserver;
use App\Observers\HistoryObserver;
use Porabote\Auth\Auth;

class HistoryLocal extends Model
{
    protected $table = 'histories';
    public $timestamps = false;

    public static function boot() {
        parent::boot();
        History::observe(AuthObserver::class);
        HistoryLocal::observe(HistoryObserver::class);
    }

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'date_created' => '',
    ];

//    protected $dates = [
//        'date_created',
//    ];

    protected $fillable = [
        'date_created',
        'model_alias',
        'record_id',
        'msg',
        'diff',
    ];

//    public function setDateCreated($value)
//    {
//        $this->attributes['date_created'] = date('Y-m-d H:i:s');
//    }
//    public function setUpdatedAtAttribute($value)
//    {
//        $this->attributes['updated_at'] = date('Y-m-d H:i:s');
//    }

    public static function setDiff($dataBefore, $dataAfter)
    {
        $diffArray = [];
        foreach ($dataAfter as $rowName => $value) {
            if (array_key_exists($rowName, $dataBefore) && $dataBefore[$rowName] != $value) {
                $diffArray[$rowName] = [
                    'before' => $dataBefore[$rowName],
                    'after' => $value,
                ];
            }
            if (isset($diffArray['updated_at'])) unset($diffArray['updated_at']);
            if (isset($diffArray['created_at'])) unset($diffArray['created_at']);
        }
        return json_encode($diffArray);
    }

}