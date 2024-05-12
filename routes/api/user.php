<?php

use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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




Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

Route::get('sendWelcomeEmail',[\App\Http\Controllers\EmailController::class,'sendWelcomeEmail']);
Route::post('request_otp',[\App\Http\Controllers\EmailController::class,'requestOtp']);
Route::post('verify_otp',[\App\Http\Controllers\EmailController::class,'verifyOtp']);

Route::post('user/register', [PassportAuthController::class, 'register'])->name('register');
Route::post('user/login', [PassportAuthController::class, 'userLogin'])->name('userLogin');



Route::group( ['prefix' =>'user','middleware' => ['auth:user-api','scopes:user'] ],function(){
   // authenticated staff routes here

//    Route::get('dashboard',[PassportAuthController::class, 'userDashboard']);
    Route::get('logout',[PassportAuthController::class,'logout'])->name('userLogout');



  //Project
  Route::prefix("projects")->group(function (){
    Route::post('/',[\App\Http\Controllers\ProjectController::class,'store']);
    Route::post('update/{id}',[\App\Http\Controllers\ProjectController::class,'update']);
  });


    Route::prefix('Transaction')->group(function () {
        // طلب معاملة جديدة
        Route::post('{id}/request', [\App\Http\Controllers\TransactionController::class, 'requestTransaction']);
        Route::get('/user-transactions', [\App\Http\Controllers\TransactionController::class,'userTransactions']);

    });
//Report
    Route::prefix("reports")->group(function (){
        Route::post('/',[\App\Http\Controllers\ReportController::class,'store']);
        Route::get('/show',[\App\Http\Controllers\ReportController::class,'userReports']);
        Route::post('update/{id}',[\App\Http\Controllers\ReportController::class,'update']);
        Route::post('delete/{id}', [\App\Http\Controllers\ReportController::class, 'destroy']);

    });



});




