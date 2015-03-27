<?php namespace App\Http\Controllers;

use App\Contracts\RideService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Welcome Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders the "marketing page" for the application and
    | is configured to only allow guests. Like most of the other sample
    | controllers, you are free to modify or remove it as you desire.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RideService $ride)
    {
        $this->ride = $ride;
        $this->middleware('auth', ['only' => ['report']]);
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function report()
    {
        $user = Auth::user();
        $report = Cache::remember('report.'.$user->id, 5, function() use ($user) {
            return $this->ride->getReport($user);
        });

        return view('report', ['report' => $report]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('home');
    }
}
