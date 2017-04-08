<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormConfirmation;
use App\Mail\ContactFormSubmission;
use Illuminate\Support\Facades\Mail;

class ContactFormController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showForm()
    {
        return view('public.contact');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function contact()
    {
        if (session()->has('submittedContact')) {
            abort(422);
        }

        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required|numeric',
            'message' => 'required'
        ]);

        Mail::send(new ContactFormSubmission(request()->all(), config('mail')));
        Mail::send(new ContactFormConfirmation(request()->all(), config('mail')));

        session()->put('submittedContact', 1);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
