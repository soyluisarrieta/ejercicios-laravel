<?php

namespace App\Http\Controllers;

use App\Models\Plate;
use App\Http\Requests\StorePlateRequest;
use App\Http\Requests\UpdatePlateRequest;

class PlateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Plate $plate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlateRequest $request, Plate $plate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plate $plate)
    {
        //
    }
}
