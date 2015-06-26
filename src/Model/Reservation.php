<?php namespace bookMe\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Reservation extends Eloquent {

    protected $table = 'reservations';
    protected $primaryKey = 'rid';
    protected $guarded = ['rid'];
    public $timestamps = false;

    /**
     * Establish an Eloquent relationship to the parent Property model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo('bookMe\Model\Property', 'pid');
    }

    /**
     * Establish an Eloquent relationship to the parent Customer model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo('bookMe\Model\Customer', 'cid');
    }


    /**
     * Return a collection of properties with date conflicts.
     *
     * @param $property
     * @param $checkIn
     * @param $checkOut
     * @return mixed
     */
    public function checkDateConflicts($property, $checkIn, $checkOut)
    {
        return $this->where('pid', '=', $property)
            ->where(function($query) use($checkIn, $checkOut)
            {
                return $query->whereBetween('check_in', array($checkIn, $checkOut))
                ->orWhereBetween('check_out', array($checkIn, $checkOut));
            })->get();
    }

    /**
     * Get list of reservations with property and customer.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function getAllReservations()
    {
        return $this->with('property', 'customer')->get();
    }

    /**
     * Get formatted status attribute.
     *
     * @return string
     */
    public function getFormattedStatus()
    {
        switch($this->status)
        {
            case 0:
                $status = 'pending';
                break;
            case 1:
                $status = 'confirmed';
                break;
            case 2:
                $status = 'paid';
                break;
            case 3:
                $status = 'cancelled';
                break;
            default:
                $status = 'pending';
        }

        return ucfirst($status);
    }

    /**
     * Get the length of the stay in days.
     *
     * @return bool|\DateInterval
     */
    public function getLengthOfStay()
    {
        $checkIn = date_create($this->check_in);
        $checkOut = date_create($this->check_out);

        return date_diff($checkIn, $checkOut)->days;
    }
}