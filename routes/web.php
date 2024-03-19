<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

Route::get('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
Route::post('/auth', [App\Http\Controllers\LoginController::class, 'auth'])->name('auth');
Route::get('/googleAuth', [App\Http\Controllers\LoginController::class, 'googleAuth'])->name('googleAuth');

Route::get('/register', [App\Http\Controllers\UserController::class, 'register'])->name("register");
Route::post('/register/store', [App\Http\Controllers\UserController::class, 'store']);

Route::get('/comics', [App\Http\Controllers\ComicsController::class, 'index']);
Route::get('/comics/{id_comic}/like', [App\Http\Controllers\LikeComicController::class, 'toggle']);

Route::get('/characters', [App\Http\Controllers\CharactersController::class, 'index']);
Route::get('/characters/{id_character}/like', [App\Http\Controllers\LikeCharacterController::class, 'toggle']);

Route::get('/favorites', [App\Http\Controllers\FavoritesController::class, 'index']);