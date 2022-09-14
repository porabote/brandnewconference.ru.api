<?php

namespace App\Models;

use App\Observers\AuthObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakersTopics extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        "speakers_id",
        "timings_topics_id",
    ];

}