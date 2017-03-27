<?php

namespace App\Core;

use App\Core\Property\Property;
use App\Exceptions\AlreadyReservedException;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $guarded = [];

    protected $dates = ['date_start', 'date_end'];

    protected $with = ['property'];

    protected static $rules = [
        'date_start' => 'required|date',
        'date_end' => 'required|date'
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return self::$rules;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return mixed
     */
    public function getLengthOfStay()
    {
        return $this->date_end->diffInDays($this->date_start);
    }

    /**
     * @param $paymentGateway
     * @param $token
     * @return $this
     */
    public function complete($paymentGateway, $token)
    {
        $chargeId = $paymentGateway->charge($this->calculateTotal(), $token);
        $this->update([
            'status' => 'paid',
            'amount' => $paymentGateway->getTotalCharges(),
            'charge_id' => $chargeId
        ]);
        return $this;
    }

    /**
     * @return bool
     */
    public function cancel()
    {
        return $this->update([
            'status' => 'cancelled'
        ]);
    }

    /**
     * @return mixed
     */
    public function calculateTotal()
    {
        return ($this->property->rate * $this->getLengthOfStay());
    }

    /**
     * @return mixed
     */
    public function getFormattedDateStartAttribute()
    {
        return $this->date_start->toFormattedDateString();
    }

    /**
     * @return mixed
     */
    public function getFormattedDateEndAttribute()
    {
        return $this->date_end->toFormattedDateString();
    }

    /**
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format(($this->amount / 100), 2);
    }

    /**
     * @param $status
     * @param $amount
     * @return $this
     */
    public function updateReservation($status, $amount, $dateStart = null, $dateEnd = null)
    {
        if ($dateStart != $this->date_start || $dateEnd != $this->date_end) {
            if (!$this->property->isAvailableBetween($dateStart, $dateEnd, $this->id)) {
                throw new AlreadyReservedException();
            }
        }

        $this->update([
            'date_start' => (null == $dateStart ? $this->date_start : $dateStart),
            'date_end' => (null == $dateEnd ? $this->date_end : $dateEnd),
            'status' => (null == $status ? $this->status : $status),
            'amount' => (null == $amount ? $this->amount : $amount),
        ]);

        return $this;
    }

    /**
     * @param $query
     * @param $reservationId
     * @return $this
     */
    public function scopeExcluding($query, $reservationId)
    {
        if (null != $reservationId) {
            return $query->where('id', '!=', $reservationId);
        }
        return $this;
    }
}
