<?php namespace bookMe\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Customer extends Eloquent {

    protected $table = 'customers';
    protected $primaryKey = 'cid';
    protected $guarded = ['cid'];
    public $timestamps = false;

    /**
     * Establish an Eloquent relationship with the child Reservation model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany('bookMe\Model\Reservation', 'cid', 'cid');
    }
}