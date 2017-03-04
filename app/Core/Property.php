<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $guarded = [];

    protected $with = ['state'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * @return string
     */
    public function getFormattedRateAttribute()
    {
        return money_format('%.2n', $this->rate);
    }

    /**
     * @return string
     */
    public function getFormattedAddressAttribute()
    {
        return $this->street_address_line_1 . PHP_EOL .
            ((null != $this->street_address_line_2) ? $this->street_address_line_2 . PHP_EOL : '').
            $this->city . ', ' . $this->state->abbreviation . ' ' . $this->zip;
    }

    /**
     *
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
