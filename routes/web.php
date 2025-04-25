<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaderboardController;

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

Route::get('/', [LeaderboardController::class, 'index'])->name('leaderboard.index');
Route::get('/recalculate', [LeaderboardController::class, 'recalculate'])->name('leaderboard.recalculate');