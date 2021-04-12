<?php

use App\Http\Controllers\CommunityController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::resource('communities', CommunityController::class);
});

require __DIR__ . '/auth.php';
