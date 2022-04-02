<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUsers extends Model
{
    protected $connection = 'auth_mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $fillable = [
        'email',
        'name',
        'post_name'
    ];

    public function user()
    {
        return $this->belongsToMany(Dialogs::class);
//            ->as('subscription')
//            ->withTimestamps();
    }

}