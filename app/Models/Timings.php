<?php

namespace App\Models;

use App\Observers\AuthObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timings extends Model
{
    protected $fillable = [
        "name",
        "start_from",
        "desc_player",
        "desc_list",
        "speaker_id",
        "datetime_from",
        "datetime_to",
        "parent_id",
        "lft",
        "rght",
    ];

    public function subblocks()
    {
        return $this->hasMany(Timings::class, 'parent_id', 'id' )
            ->orderByDesc('lft');
    }

    public function topics()
    {
        return $this->hasMany(TimingsTopics::class, 'timing_id', 'id' )
            ->orderByDesc('lft');
    }

}