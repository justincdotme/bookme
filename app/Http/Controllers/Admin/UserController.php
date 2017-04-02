<?php

namespace App\Http\Controllers\Admin;

use App\Core\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        if (Gate::denies('create-admin-user', auth()->user())) {
            abort(403);
        }
        $this->validate(request(), User::getRules());

        $user = User::createAdminUser(request('email'), request('password'));

        return response()->json([
            'status' => 'success',
            'user' => $user
        ]);
    }
}
