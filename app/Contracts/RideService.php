<?php namespace App\Contracts;

use Illuminate\Http\Request;

interface RideService
{
    public function loginFlow(Request $request);
}
