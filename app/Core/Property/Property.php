<?php

namespace App\Core\Property;

use App\Core\Reservation;
use App\Core\State;
use App\Exceptions\AlreadyReservedException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $guarded = [];

    protected $with = ['state'];

    public $rules = [
        'rate' => 'integer',
        'name' => 'required',
        'status' => 'required',
        'street_address_line_1' => 'required',
        'city' => 'required',
        'state_id' => 'required|integer',
        'zip' => 'required|integer'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('available', function (Builder $builder) {
            $builder->where('status', 'available');
        });
    }

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    /**
     * @return Model
     */
    public function makeImage()
    {
        return $this->images()->create([]);
    }

    /**
     * @return string
     */
    public function getFormattedRateAttribute()
    {
        return money_format('%.2n', ($this->rate / 100));
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
     * @param $dateStart
     * @param $dateEnd
     * @param $user
     * @return mixed
     */
    public function reserveFor($dateStart, $dateEnd, $user)
    {
        if ($this->isAvailableBetween($dateStart, $dateEnd)) {
            return Reservation::create([
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
