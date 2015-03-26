<?php namespace App\Http\Controllers;

use App\Contracts\RideService;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RideService $ride)
    {
        $this->ride = $ride;
        $this->middleware('guest');
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $access_token = $this->ride->loginFlow($request);
        return redirect()->route('report');
    }
}
