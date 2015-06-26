<?php namespace bookMe\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Property extends Eloquent {


    protected $table = 'properties';
    protected $primaryKey = 'pid';
    protected $guarded = ['pid'];
    public $timestamps = false;

    /**
     * Return full list of properties with images.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getProperties()
    {
        return $this->with('images')->get();
    }

    /**
     * Return a single property with images.
     *
     * @param $id
     * @return mixed
     */
    public function getProperty($id)
    {
        return $this->where('pid', '=', $id)->with('images')->first();
    }

    /**
     * Establish an Eloquent relationship with the child PropertyImage model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('bookMe\Model\PropertyImage', 'pid', 'pid');
    }

    /**
     * Establish an Eloquent relationship with the child Reservation model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany('bookMe\Model\Reservation', 'pid', 'pid');
    }

    /**
     * Format the rate with a dollar sign.
     * Return DEFAULT_PRICE_TEXT if not set.
     *
     * @return string
     */
    public function getFormattedRate()
    {
        if(!is_null($this->attributes['rate']) && !empty($this->attributes['rate']))
        {
            return '$' . $this->attributes['rate'];
        }
        return DEFAULT_PRICE_TEXT;
    }

    /**
     * Return a default value if short_desc is not set.
     *
     * @param $value
     * @return string
     */
    public function getShortDescAttribute($value)
    {
        if(!is_null($value) && !empty($value))
        {
            return $value;
        }
        return MISSING_DESC_TEXT;
    }

    /**
     * Return a default value if long_desc is not set.
     *
     * @param $value
     * @return string
     */
    public function getLongDescAttribute($value)
    {
        if(!is_null($value) && !empty($value))
        {
            return $value;
        }
        return MISSING_DESC_TEXT;
    }

    /**
     * Set the rate col to NULL if value is empty string.
     *
     * @param $value
     */
    public function setRateAttribute($value)
    {
        $rate = $value !== '' ? $value : null;
        $this->attributes['rate'] = $rate;
    }
}