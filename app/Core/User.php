<?php

namespace App\Core;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable;
    use CanResetPasswordTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $with = ['phones'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @var array
     */
    protected static $rules = [
        'email' => 'required',
        'password' => 'required|confirmed'
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phones()
    {
        return $this->hasMany(Phone::class);
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

    /**
     * @param array $user
     * @return mixed
     */
    public static function createStandardUser(array $user)
    {
        return self::create([
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'password' => bcrypt($user['password']),
            'role_id' => 1
        ]);
    }

    /**
     * @param $email
     * @param $password
     * @return mixed
     */
    public static function createAdminUser($email, $password)
    {
        return self::create([
            'email' => $email,
            'password' => bcrypt($password),
            'role_id' => 2
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
