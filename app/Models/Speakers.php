<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Speakers extends Model
{
    use NodeTrait;

    protected $fillable = [
        'name',
        'last_name',
        'post_name',
        'patronymic',
    ];

    public function getLftName()
    {
        return 'lft';
    }

    public function getRgtName()
    {
        return 'rght';
    }

    public function getParentIdName()
    {
        return 'parent_id';
    }

    // Specify parent id attribute mutator
    public function setParentAttribute($value)
    {
        $this->setParentIdAttribute($value);
    }
    //Menus::fixTree();

    public function avatar()
    {
        return $this->hasOne(Files::class, 'record_id', 'id' )
            ->where('model_alias', 'Speakers')
            ->orderByDesc('id')
            ->where('label', 'avatar');
    }

}