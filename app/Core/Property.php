<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model;
use \App\Exceptions\AlreadyReservedException;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
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
            ((null != $this->street_address_line_2) ? $this->street_address_line_2 . PHP_EOL : '') .
            $this->city . ', ' . $this->state->abbreviation . ' ' . $this->zip;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * @param $dateStart
     * @param $dateEnd
     * @return mixed
     */
    public function isAvailableBetween($dateStart, $dateEnd)
    {
        return $this->reservations()->active()->where(function ($query) use ($dateStart, $dateEnd) {
            return $query->whereBetween('date_start', [$dateStart, $dateEnd])
                ->orWhereBetween('date_end', [$dateStart, $dateEnd]);
        })->get()->isEmpty();
    }

    /**
     * @param $user
     * @param $dateStart
     * @param $dateEnd
     * @return \App\Core\Reservation
     * @throws AlreadyReservedException
     */
    public function makeReservation($user, $dateStart, $dateEnd)
    {
        if ($this->isAvailableBetween($dateStart, $dateEnd)) {
            return $this->reservations()->create([
                'property_id' => $this->id,
                'user_id' => $user->id,
                'status' => 'pending',
                'date_start' => $dateStart,
                'date_end' => $dateEnd
            ]);
        }

        throw new AlreadyReservedException();
    }
}
