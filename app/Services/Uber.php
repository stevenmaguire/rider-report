<?php namespace App\Services;

use App\Contracts\RideService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Url;
use Illuminate\Http\Request;
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
     * Create new service instance.
     */
    public function __construct()
    {
        session_start();
        $this->client = new UberApi([
            'server_token' => Config::get('services.uber.server_token'),
            'use_sandbox'  => true, // optional, default false
        ]);

        $this->oauth = new UberOAuth([
            'clientId'          => Config::get('services.uber.client_id'),
            'clientSecret'      => Config::get('services.uber.client_secret'),
            'redirectUri'       => Url::route('oauth.uber'),
            'scopes'            => ['profile history']
        ]);
    }

    public function loginFlow(Request $request)
    {
        if (!$request->input('code')) {

            // If we don't have an authorization code then get one
            $authUrl = $this->oauth->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $this->oauth->state;
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($request->input('state')) || ($request->input('state') !== $_SESSION['oauth2state'])) {

            unset($_SESSION['oauth2state']);
            exit('Invalid state');

        } else {

            // Try to get an access token (using the authorization code grant)
            $token = $this->oauth->getAccessToken('authorization_code', [
                'code' => $request->input('code')
            ]);

            // Optional: Now you have a token you can look up a users profile data
            try {
                return $token->accessToken;

                // We got an access token, let's now get the user's details
                //$userDetails = $this->oauth->getUserDetails($token);

                // Use these details to create a new profile
                //printf('Hello %s!', $userDetails->firstName);

            } catch (Exception $e) {

                // Failed to get user details
                exit('Oh dear...');
            }
        }
    }
}
