<?php

use App\Http\Controllers\Api\ChatController;
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

// Route::middleware('auth:api')->get('/chat', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' =>'auth:api','prefix'=>'/user/chat'],function (){
    Route::get('rooms',[ChatController::class,'rooms']);
    Route::get('rooms/{user_id}/messages',[ChatController::class,'messages']);
    Route::post('room/{user_id}/message',[ChatController::class,'sendMessage']);
    Route::post('rooms/delete/{id}',[ChatController::class,'deleteConversation']);
});
