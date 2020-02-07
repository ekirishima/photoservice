<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authenfication.
Route::post('signup', 'AuthController@signup');
Route::post('login', 'AuthController@login');

// Only authenfication requests.
Route::group(['middleware' => ['auth:api']], function () {

    // Logout
    Route::post('logout', 'AuthController@logout');

    // Photos
    Route::prefix('photo')->group(function() {
        Route::post('/', 'PhotosController@upload');
        Route::match(['PATCH', 'POST'], '{id}', 'PhotosController@edit');
        Route::get('/', "PhotosController@index");
        Route::get('{id}', "PhotosController@photo");
        Route::delete('{id}', "PhotosController@delete");
    });

    // User 
    Route::prefix('user')->group(function() {
        Route::post('{user}/share', "PhotosController@share");
        Route::get('/', "UserController@search");
    });

});