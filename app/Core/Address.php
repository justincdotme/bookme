<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $guarded = [];

    protected static $rules = [
        'line1' => 'required',
        'city' => 'required',
        'state_id' => 'required|integer',
        'zip' => 'required|numeric'
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return self::$rules;
    }
}