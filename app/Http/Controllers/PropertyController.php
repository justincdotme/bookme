<?php

namespace App\Http\Controllers;

use App\Core\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display the specified resource.
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

    public function store()
    {
        return Property::create(request()->except('id'));
    }
}
