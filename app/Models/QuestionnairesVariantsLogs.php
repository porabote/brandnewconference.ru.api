<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnairesVariantsLogs extends Model
{
    protected $fillable = [
        'questionnaires_id',
        'ip',
    ];

}