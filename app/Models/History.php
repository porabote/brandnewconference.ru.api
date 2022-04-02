<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Observers\AuthObserver;

class History extends Model
{
    protected $connection = 'api_mysql';

    public static function boot() {
        parent::boot();
        History::observe(AuthObserver::class);
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'created_at' => '',
        'updated_at' => '',
        'flag' => 'on',
        'user_name' => ''
    ];

    protected $fillable = [
        'model_alias',
        'record_id',
        'msg',
        'diff',
    ];

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = date('Y-m-d H:i:s');
    }
    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = date('Y-m-d H:i:s');
    }

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