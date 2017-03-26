<?php

namespace App\Core;

use App\Core\Property\Property;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $guarded = [];

    protected $dates = ['date_start', 'date_end'];

    protected $with = ['property'];
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
}
