<?php

namespace App\Http\Controllers;

use App\Core\User;
use App\Mail\UserRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store()
    {
        $this->validate(request(), array_merge(
            User::getRules(), [
            'first_name'=> 'required',
            'last_name'=> 'required',
            ])
        );

        $user = User::createStandardUser([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'email' => request('email'),
            'password' => request('password')
        ]);

        Mail::to($user)->send(new UserRegistration($user, config('mail')));

        return redirect()->route('login');
    }
}