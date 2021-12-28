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
        Route::get('/achievement', 'CalonSiswaController@achievement');
        Route::get('/csbpotensial', 'CalonSiswaController@CsbAchievement');
        Route::get('/listfilter', 'CalonSiswaController@ListFilter');
        Route::post('/change', 'CalonSiswaController@change');
        Route::post('/filter', 'CalonSiswaController@filter');
        Route::post('/filterag', 'CalonSiswaController@filterag');
        Route::post('/filterask', 'CalonSiswaController@filterask');
        Route::post('/filterach', 'CalonSiswaController@filterAch');
        Route::post('/filtercsb', 'CalonSiswaController@filterCsb');
    });

    Route::group(['prefix' => 'siswa'], function() {
        Route::get('/trend', 'SiswaController@trendsiswa');
        Route::get('/class', 'SiswaController@ClassOcp');
        Route::get('/agama', 'SiswaController@agama');
        Route::get('/listfiltersiswa', 'SiswaController@ListFilterSiswa');
        Route::get('/jmlkelastingkat', 'SiswaController@JmlKelasTingkat');
        Route::get('/siblings', 'SiswaController@siblings');
        Route::post('/change', 'SiswaController@change');
        Route::post('/filtertrend', 'SiswaController@FilterTrend');
        Route::post('/filterag', 'SiswaController@FilterAg');
        Route::post('/filtertkt', 'SiswaController@FilterTkt');
        Route::post('/filtersibling', 'SiswaController@filtersibling');
    });
    Route::group(['prefix' => 'kepegawaian'], function() {
        Route::get('/agama', 'KepegawaianController@AgamaPegawai');
        Route::get('/listfilterpegawai', 'KepegawaianController@ListFilterPegawai');
        Route::get('/pendidikan', 'KepegawaianController@PendidikanPegawai');
        Route::get('/masakerja', 'KepegawaianController@MasaKerjaPegawai');
        Route::get('/keluargapegawai', 'KepegawaianController@KeluargaPegawai');
        // Route::get('/jmlkelastingkat', 'SiswaController@JmlKelasTingkat');
        // Route::get('/siblings', 'SiswaController@siblings');
        // Route::post('/change', 'SiswaController@change');
        // Route::post('/filtertrend', 'SiswaController@FilterTrend');
        // Route::post('/filterag', 'SiswaController@FilterAg');
        // Route::post('/filtertkt', 'SiswaController@FilterTkt');
        // Route::post('/filtersibling', 'SiswaController@filtersibling');
    });

    // Route::group(['prefix' => 'siswa'], function() {
    //     Route::get('/trend', 'SiswaController@trendsiswa');
    //     Route::get('/class', 'SiswaController@ClassOcp');
    //     Route::get('/agama', 'SiswaController@agama');
    //     Route::get('/listfiltersiswa', 'SiswaController@ListFilterSiswa');
    //     Route::get('/jmlkelastingkat', 'SiswaController@JmlKelasTingkat');
    //     Route::get('/siblings', 'SiswaController@siblings');
    //     Route::post('/change', 'SiswaController@change');
    //     Route::post('/filtertrend', 'SiswaController@FilterTrend');
    //     Route::post('/filterag', 'SiswaController@FilterAg');
    //     Route::post('/filtertkt', 'SiswaController@FilterTkt');
    //     Route::post('/filtersibling', 'SiswaController@filtersibling');
    // });


});
