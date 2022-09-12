<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questionnaires extends Model
{
    protected $fillable = [
        'question',
    ];

    public function variants()
    {
        return $this->hasMany(QuestionnairesVariants::class, 'questionnaires_id', 'id' )
            ->orderByDesc('lft');
    }

}