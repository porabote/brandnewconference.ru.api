<?php

namespace App\Models;

use App\Observers\AuthObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimingsTopics extends Model
{
    protected $fillable = [
        "desc",
        "desc_short",
        "timing_id",
        "datetime_from",
        "datetime_to",
        "start_from",
    ];

    public function speakers()
    {
        return $this->belongsToMany(Speakers::class, 'speakers_topics')
            ->orderBy('speakers_topics.lft');
    }

}