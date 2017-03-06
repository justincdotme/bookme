<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $guarded = [];

    protected $dates = ['date_start', 'date_end'];
    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
}
