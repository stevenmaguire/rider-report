<?php namespace App\Services;

use App\Contracts\RideService;
use App\Report;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Url;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Stevenmaguire\Uber\Client as UberApi;
use Stevenmaguire\OAuth2\Client\Provider\Uber as UberOAuth;

class Uber implements RideService
{
    /**
     * Uber API Client
     *
     * @var UberApi
     */
    public $client;

    /**
     * Uber OAuth Client
     *
     * @var UberOAuth
     */
    public $oauth;

    /**
     * Session state key
     *
     * @var string
     */
    private $state_session_key = 'oauth2state';

    /**
     * Create new service instance.
     */
    public function __construct()
    {
        $this->client = new UberApi([
            'server_token' => Config::get('services.uber.server_token'),
            'use_sandbox'  => false, // optional, default false
        ]);

        $this->oauth = new UberOAuth([
            'clientId'          => Config::get('services.uber.client_id'),
            'clientSecret'      => Config::get('services.uber.client_secret'),
            'redirectUri'       => Url::route('oauth.uber'),
            'scopes'            => ['profile history_lite']
        ]);
    }

    /**
     * Generate history report for user
     *
     * @param  User    $user
     *
     * @return Report
     */
    public function getReport(User $user)
    {
        $report = new Report;
        $continue = true;
        $limit = 50;
        $total = null;
        $found = 0;
        $this->client->setAccessToken($user->uber_token)->setVersion('v1.1');

        $append_to_report = function ($trip) use ($report, &$found) {
            $report->addUberTrip($trip);
            $found++;
        };

        while ($continue) {
            $history = $this->client->getHistory(['limit' => $limit, 'offset' => $found]);
            if (is_null($total)) {
                $total = $history->count;
            }
            array_map($append_to_report, $history->history);
            if ($found >= $total) {
                $continue = false;
            }
        }

        return $report;
    }

    /**
     * Get user with token
     *
     * @param  string $token
     *
     * @return User|null
     */
    public function getUser($token)
    {
        $this->client->setAccessToken($token);
        $response = $this->client->getProfile();
        $user = User::where('email', $response->email)->first();
        if ($user) {
            $user->name = $response->first_name.' '.$response->last_name;
            $user->uber_token = $token;
            $user->save();
        } else {
            User::create([
                'email' => $response->email,
                'name' => $response->first_name.' '.$response->last_name,
                'uber_token' => $token
            ]);
        }

        return $user;
    }

    /**
     * Coordinate steps of the OAuth login flow
     *
     * @param  Request  $request
     *
     * @return RedirectResponse|string
     * @throws \Exception
     */
    public function login(Request $request)
    {
        if (!$request->input('code')) {

            $authUrl = $this->oauth->getAuthorizationUrl();

            Session::put($this->state_session_key, $this->oauth->state);

            return redirect($authUrl);

        } elseif (empty($request->input('state')) || ($request->input('state') !== Session::get($this->state_session_key))) {

            Session::forget($this->state_session_key);

            throw new \Exception;

        } else {

            $token = $this->oauth->getAccessToken('authorization_code', [
                'code' => $request->input('code')
            ]);

            $user = $this->getUser($token->accessToken);

            if ($user) {
                Auth::login($user);
            }
        }
    }
}
