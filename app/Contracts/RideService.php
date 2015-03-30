<?php namespace App\Contracts;

use App\User;
use Illuminate\Http\Request;

interface RideService
{
    public function getVendor();
    public function getReport(User $user);
    public function getUser($token);
    public function login(Request $request);
}
