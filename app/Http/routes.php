<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'home', 'uses' => 'WelcomeController@index']);
Route::get('/report', ['as' => 'report', 'uses' => 'WelcomeController@report']);
Route::get('/oauth/uber', ['as' => 'oauth.uber', 'uses' => 'OAuthController@index']);
Route::get('/logout', ['as' => 'logout', 'uses' => 'WelcomeController@logout']);
