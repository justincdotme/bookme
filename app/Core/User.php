<?php

namespace App\Core;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static $rules = [
        'email' => 'required',
        'password' => 'required'
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return self::$rules;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return ($this->role_id == 2);
    }

    /**
     * @return bool
     */
    public function isStandard()
    {
        return ($this->role_id == 1);
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role->name;
    }

    public static function createStandardUser(array $user)
    {
        return self::create([
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'password' => bcrypt($user['password']),
        ]);
    }
}
