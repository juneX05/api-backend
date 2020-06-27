<?php

use Illuminate\Http\Request;
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
Route::middleware('guest')->group(function () {
//    Route::post('register', 'AuthController@register')->name('register');
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('refresh-token', 'AuthController@refreshToken')->name('refreshToken');
});

Route::middleware('auth:api')->group(function () {
    Route::get('/user', 'AuthController@user');
    Route::post('logout', 'AuthController@logout');

    Route::apiResource('users', 'UserController');
    Route::post('users/remove/profile_picture', 'UserController@removeProfilePicture');
    Route::apiResource('roles', 'RoleController');
    Route::apiResource('permissions', 'PermissionController');
    Route::apiResource('files', 'FileController');
    Route::post('files/check', 'FileController@check');
    Route::apiResource('fileExtensions', 'FileExtensionController');

    Route::put('/profile/picture/remove', 'ProfileController@removeProfilePicture');
    Route::put('/profile/picture/update', 'ProfileController@updateProfilePicture');
    Route::put('/profile/change/password', 'ProfileController@updatePassword');
    Route::put('/profile/change/info', 'ProfileController@updateMyInfo');
});

