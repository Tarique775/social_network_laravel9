<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::post('user_registration', [RegistrationController::class, 'create'])->name('user_create');

Route::controller(AuthController::class)->group(function(){

    Route::post('registration', 'registration');

    Route::post('login','login');
});
//Route::post('/user_registration', [UserController::class, 'create'])->name('user_create');
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::post('/user_registration', [UserController::class, 'create'])->name('user_create');

//Route::post('/login',[UserController::class,'login'])->name('login');

Route::middleware('auth:sanctum')->group(function(){

    Route::get('/getdata', [UserController::class, 'getdata'])->name('showdata');

    Route::post('/post', [UserController::class, 'posts'])->where('id', '[0-9]+')->name('Insert_post');

    Route::put('/updatepost/{postId}', [UserController::class, 'updatePost'])->where(['postId' => '[0-9]+', 'userId' => '[0-9]+'])->name('Update_post');

    Route::delete('/deletepost/{postId}', [UserController::class, 'deletePost'])->where(['postId' => '[0-9]+', 'userId' => '[0-9]+'])->name('Delete_post');

    Route::post('/comment/{postId}', [UserController::class, 'comments'])->where(['postId' => '[0-9]+', 'userId' => '[0-9]+'])->name('Insert_comment');

    Route::put('/updatecomment/{commentId}', [UserController::class, 'updateComments'])->where(['postId' => '[0-9]+', 'userId' => '[0-9]+'])->name('Update_comment');

    Route::delete('/deletecomment/{commentId}', [UserController::class, 'deleteComments'])->where(['postId' => '[0-9]+', 'userId' => '[0-9]+'])->name('Delete_comment');

    Route::post('/replie_comment/{commentId}', [UserController::class, 'replieComments'])->where(['commentId' => '[0-9]+', 'userId' => '[0-9]+'])->name('Insert_replie_comment');

    Route::get('/postlike/{postId}', [UserController::class, 'postlikes'])->where(['postId' => '[0-9]+', 'userId' => '[0-9]+'])->name('PostLikeDislike');

    Route::post('/role',[UserController::class,'roles'])->name('User_Role');

    Route::post('/roleusers/{roleId}',[UserController::class,'roleUsers'])->name('Role_Users');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});
