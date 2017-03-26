<?php

namespace App\Http\Controllers;

use App\Core\Property\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        return view('public.properties.index', [
            'properties' => Property::available()->paginate(10)
        ]);
    }

    /**
     * Show a property
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('public.properties.show', [
            'property' => Property::available()->findOrFail($id)
        ]);
    }
}