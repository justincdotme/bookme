<?php

namespace App\Http\Controllers;

use App\Core\Phone;
use App\Core\User;
use App\Mail\UserRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), array_merge(
                User::getRules(), [
                'first_name'=> 'required',
                'last_name'=> 'required',
                'phone' => 'required|numeric'
            ])
        );

        $user = User::createStandardUser([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'email' => request('email'),
            'password' => request('password')
        ]);

        $phone = Phone::create([
            'phone' => request('phone')
        ]);
        $user->phones()->save($phone);

        Mail::send(new UserRegistration($user, config('mail')));

        return redirect()->route('login');
    }

    /**
     * @param $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($user)
    {
        if (Gate::denies('show-user', $user)) {
            abort(403);
        }
        return view('public.users.show', [
            'user' => $user
        ]);
    }

    /**
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($user)
    {
        if (Gate::denies('update-user', $user)) {
            abort(403);
        }

        $this->validate(request(), [
           'first_name' => 'required',
           'last_name' => 'required',
           'phone' => 'required|numeric'
        ]);

        $user->update(request()->only(
            'first_name',
            'last_name'
        ));
        $user->phones()->first()->update([
            'phone' => request('phone')
        ]);

        return response()->json([
            'status' => 'success',
            'user' => $user->fresh()
        ]);
    }
}