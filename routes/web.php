<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\EventController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//カレンダー表示画面
Route::controller(ScheduleController::class)->middleware(['auth'])->group(function(){
    Route::get('/calendar', 'show')->name('show');
    Route::post('/calendar/create', 'create')->name('create'); //予定追加
    Route::post('/calendar/get', 'get')->name('get'); //DBの予定を反映
});

//イベント表示画面
Route::controller(EventController::class)->middleware(['auth'])->group(function(){
    Route::get('/events/create', 'newEvent')->name('newEvent'); //イベント作成画面の表示
    Route::post('/events/create', 'createEvent')->name('createEvent'); //予定追加
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
