<?php

use App\Http\Controllers\CommunityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostVoteController;
use App\Http\Controllers\Profile\CommunityController as ProfileCommunityController;
use App\Http\Controllers\Profile\ProfileController;
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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'communities', 'as' => 'community.'], function () {
    Route::get('', [CommunityController::class, 'index'])->name('index');
    Route::get('{community}', [CommunityController::class, 'show'])->name('show');
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('communities.posts', PostController::class)->except('show');
    Route::resource('communities.posts.votes', PostVoteController::class)->only('store');
    Route::resource('communities.posts.comments', PostCommentController::class)->except(['show', 'index']);
});

Route::get('/communities/{community}/posts/{post}', [PostController::class, 'show'])->name('communities.posts.show');

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth', 'verified']], function () {
    Route::get('', [ProfileController::class, 'index'])->name('index');
    Route::resource('communities', ProfileCommunityController::class);
});

require __DIR__ . '/auth.php';
