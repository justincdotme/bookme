<?php

namespace App\Core;

use App\Core\Billing\PaymentFailedException;
use App\Exceptions\AlreadyReservedException;
use Carbon\Carbon;
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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLengthOfStay()
    {
        return $this->date_end->diffInDays($this->date_start);
    }

    public function complete($paymentGateway, $token)
    {
        $paymentGateway->charge($this->calculateTotal(), $token);
        return $this->update([
            'status' => 'paid',
            'amount' => $paymentGateway->getTotalCharges()
        ]);
    }

    public function cancel()
    {
        //TODO - Expand this to check for and refund any charges for the current reservation.
        return $this->update([
            'status' => 'cancelled'
        ]);
    }

    public function calculateTotal()
    {
        return ($this->property->rate * $this->getLengthOfStay());
    }
}
