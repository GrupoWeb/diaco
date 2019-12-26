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


// Rutas para apiRest

Route::get('apiRest','ServiciosRest@ApiRest');
Route::get('getSedes','ServiciosRest@getSede');
// Route::get('categoriess','ServiciosRest@getApi');
Route::get('getprecio/{id}/{categoria}','ServiciosRest@getPricePrevious');
Route::get('getprecios/{id}/{categoria}','ServiciosRest@getPriceLast');
Route::get('price/{id}/{categoria}','ServiciosRest@apiPrice');

Route::get('fecha/{sede}/{categoria}', 'ServiciosRest@getPriceLastPrevious');
Route::get('prueba/{sede}/{categoria}', 'ServiciosRest@getPriceLastPrevious');


//Route::get('categories', 'ServiciosRest@collectionDataApi');
Route::get('categories', 'ServiciosRest@VerifyActiveDepartments');
Route::get('VerifyActiveDepartments','ServiciosRest@VerifyActiveDepartments');

Route::get('verify','appsMobile@VerifyActiveDepartmentsMethods');




