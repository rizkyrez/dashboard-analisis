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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['prefix' => 'v1'], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'Auth\AuthController@login');
        Route::get('refresh', 'Auth\AuthController@refresh');
        Route::get('user', 'Auth\AuthController@user')->middleware('jwt.verify');
        Route::post('logout', 'AuthController@logout')->middleware('jwt.verify');  
    });

    Route::group(['prefix' => 'calons'], function() {
        Route::get('/trendcs', 'CalonSiswaController@trendcs');
        Route::get('/agama', 'CalonSiswaController@agama');
        Route::get('/asalsekolah', 'CalonSiswaController@exin_asalsekolah');
        Route::get('/listfilter', 'CalonSiswaController@ListFilter');
        Route::post('/change', 'CalonSiswaController@change');
        Route::post('/filter', 'CalonSiswaController@filter');
        Route::post('/filterag', 'CalonSiswaController@filterag');
        Route::post('/filterask', 'CalonSiswaController@filterask');
    });

    Route::group(['prefix' => 'siswa'], function() {
        Route::get('/trend', 'SiswaController@trendsiswa');
        Route::get('/agama', 'SiswaController@agama');
        Route::get('/listfiltersiswa', 'SiswaController@ListFilterSiswa');
        Route::post('/change', 'SiswaController@change');
        Route::post('/filtertrend', 'SiswaController@FilterTrend');
        Route::post('/filterag', 'SiswaController@FilterAg');
    });

});
