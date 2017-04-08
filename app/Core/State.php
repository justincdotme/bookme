<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $guarded = [];

    /**
     * @return mixed
     */
    public static function getList()
    {
        return State::pluck('id', 'abbreviation');
    }
}