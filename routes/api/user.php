<?php


use App\Http\Controllers\PassportAuthController;
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






});




