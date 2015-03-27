<?php namespace App\Http\Controllers;

use App\Contracts\RideService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

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
        $response = $this->ride->login($request);

        if ($response instanceof RedirectResponse) {
            return $response;
        }

        return redirect()->route('report');
    }
}
