<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return 'Working';
});

Route::group(['namespace' => 'Auth'], function () {
    Route::post('login', 'LoginController@login')->name('login');
    Route::post('password/link', 'PasswordResetController@create');
    Route::get('password/find/{token}', 'PasswordResetController@find');
    Route::post('password/reset', 'PasswordResetController@reset');

    Route::get('register/timezones', 'RegisterController@getTimezones')->name('get-timezones');
    Route::get('register/countries', 'RegisterController@getCountries')->name('get-countries');
    Route::post('register', 'RegisterController@register')->name('register');

    Route::get('email/verify/{id}', 'VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name

    Route::post('logout', 'LoginController@logout');
});

Route::group(['namespace' => 'UserPanel'], function () {
    Route::get('/current-user', 'UserController@getCurrentUser');
});



//Tenant specific routes
Route::group(['prefix' => '{tenant}', 'namespace' => 'Tenant'], function () {

    //Normal-user Routes
    Route::middleware('auth:api')->group(function () {
        //Verifications routes

        Route::group(['prefix' => 'verifications', 'namespace' => 'Verifications'], function () {
            Route::apiResource('web-verifications', 'WebVerificationController');
            Route::post('web-verifications/{web_verification}', 'WebVerificationController@runVerification');
            Route::get('web-verifications/{web_verification}/get-data', 'WebVerificationController@getVerificationData');

            Route::apiResource('dns-verifications', 'DNSVerificationController');
            Route::post('dns-verifications/{dns_verification}', 'DNSVerificationController@runVerification');
            Route::get('dns-verifications/{dns_verification}/get-data', 'DNSVerificationController@getVerificationData');

            Route::apiResource('icmp-verifications', 'ICMPVerificationController');
            Route::post('icmp-verifications/{icmp_verification}', 'ICMPVerificationController@runVerification');

            Route::apiResource('tcp-verifications', 'TCPVerificationController');
            Route::post('tcp-verifications/{tcp_verification}', 'TCPVerificationController@runVerification');
            Route::get('tcp-verifications/{tcp_verification}/get-data', 'TCPVerificationController@getVerificationData');

            Route::apiResource('snmp-verifications', 'SNMPVerificationController');
            Route::post('snmp-verifications/{snmp_verification}', 'SNMPVerificationController@runVerification');
            Route::get('snmp-verifications/{snmp_verification}/get-data', 'SNMPVerificationController@getVerificationData');

            Route::get('icmp-verifications/{icmp_verification}/get-data', 'ICMPVerificationController@getVerificationData');
        });
    });
});
