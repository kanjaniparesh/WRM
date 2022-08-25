<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShortUrlController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', [App\Http\Controllers\ShortUrlController::class, 'index'])->name('home');
    Route::post('/shorturl', [App\Http\Controllers\ShortUrlController::class, 'store'])->name('saveurl');
    Route::get('/shorturl/{id}', [App\Http\Controllers\ShortUrlController::class, 'show'] )->name('geturl');
    Route::post('/deleteshorturl', [App\Http\Controllers\ShortUrlController::class, 'destroy'] )->name('deleteurl');
    Route::get('/edit/{id}', [App\Http\Controllers\ShortUrlController::class, 'edit'] )->name('editurl');
    Route::get('/piechart', [App\Http\Controllers\ShortUrlController::class, 'piechart'] )->name('piechart');
});

Route::get('/s/{id}', [App\Http\Controllers\ShortUrlController::class, 'publiclyshow'] )->name('s');
