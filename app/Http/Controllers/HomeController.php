<?php

namespace App\Http\Controllers;

use App\Core\Property\Property;

class HomeController extends Controller
{
    /**
     * @param Property $property
     * @return \Illuminate\Http\Response
     */
    public function index(Property $property)
    {
        return view('public.home', [
            'featuredProperties' => $property->featured()->get()
        ]);
    }
}
