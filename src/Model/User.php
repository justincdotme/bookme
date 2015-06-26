<?php namespace bookMe\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent {

    protected $guarded = ['uid'];
    public $timestamps = false;
    protected $primaryKey = 'uid';

    /**
     * Find and retrieve a user by their email address.
     *
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email)
    {
        return $this->where('email', '=', $email)->first();
    }

}