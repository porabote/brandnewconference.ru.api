<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Faq extends Model
{
    use NodeTrait;

    protected $fillable = [
        'question',
        'answer',
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

}