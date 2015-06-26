<?php namespace bookMe\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PropertyImage extends Eloquent {

    protected $table = 'property_images';
    protected $primaryKey = 'img_id';
    protected $guarded = ['img_id'];
    public $timestamps = false;



    /**
     * Establish an Eloquent relationship to the parent Product model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('bookMe\model\Property', 'pid', 'pid');
    }

}