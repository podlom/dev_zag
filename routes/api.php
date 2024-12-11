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

Route::prefix('product')->group(function() {
/*
	Route::get('price/{id}', '\App\Http\Controllers\API\ProductController@price');
	Route::get('type/{id}', '\App\Http\Controllers\API\ProductController@type');
	Route::get('city/{id}', '\App\Http\Controllers\API\ProductController@city');
*/
	
	
	Route::get('cardFields/{id}', '\App\Http\Controllers\API\ProductController@cardFields');
});

// Route::prefix('company')->group(function() {
// 	Route::get('byArea/{region}/{area}/{city}', '\App\Http\Controllers\API\CompanyController@companiesByArea');
// });

// Route::prefix('article')->group(function() {
// 	Route::get('byArea/{articleTab}/{region}', '\App\Http\Controllers\API\ArticleController@byArea');
// });

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
