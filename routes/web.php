<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/chat/{any?}',[ChatController::class,'index'])->name('chat')->where('any', '.*')->middleware('auth');
Route::get('all-chat-users',[ChatController::class,'allUsers']);
Route::view('test','test');
Route::group(['middleware' =>'auth','prefix'=>'/user/chat'],function (){
    Route::get('rooms',[ChatController::class,'rooms']);
    Route::get('rooms/{user_id}/messages',[ChatController::class,'messages']);
    Route::post('room/{user_id}/message',[ChatController::class,'sendMessage']);
    Route::post('rooms/delete/{id}',[ChatController::class,'deleteConversation']);
});
