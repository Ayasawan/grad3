<?php


use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\CommunicationController;

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


<<<<<<< HEAD

Route::post('investor/{investorId}/interests', [InvestorController::class, 'addInterests']);


Route::group(['middleware' => ['auth:investor-api,user-api']], function () {


  //Investor
  Route::prefix("investors")->group(function (){
      Route::get('/{id}',[\App\Http\Controllers\InvestorController::class,'showProfileByAnother']);
  });
    Route::prefix("users")->group(function (){
        Route::get('/{id}',[\App\Http\Controllers\InvestorController::class,'showProfileByAnotherUser']);
    });

});



=======
Route::post('investor/{investorId}/interests', [InvestorController::class, 'addInterests']);


>>>>>>> cbaaa0d1eb35d4227caa64442f61c7c473c7b910
Route::post('investor/register', [PassportAuthController::class, 'registerInvestor'])->name('registerInvestor');
Route::post('investor/login', [PassportAuthController::class, 'LoginInvestor'])->name('LoginInvestor');
Route::post('verify_otpInv',[\App\Http\Controllers\PassportAuthController::class,'verifyOtpInv']);

Route::group( ['prefix' =>'investor','middleware' => ['auth:investor-api','scopes:investor'] ],function(){
   // authenticated staff routes here

//    Route::get('dashboard',[PassportAuthController::class, 'userDashboard']);
    Route::get('logout',[PassportAuthController::class,'LogoutInvestor'])->name('LogoutInvestor');



//Complaint
Route::prefix("complaints")->group(function (){

  Route::post('/',[\App\Http\Controllers\ComplaintController::class,'store']);
  Route::post('update/{id}',[\App\Http\Controllers\ComplaintController::class,'update']);
  Route::post('delete/{id}',[\App\Http\Controllers\ComplaintController::class,'destroyInvestor']);
});



//Investor
Route::prefix("investors")->group(function (){
    Route::post('update/{id}',[\App\Http\Controllers\InvestorController::class,'update']);
    Route::post('delete',[\App\Http\Controllers\InvestorController::class,'destroyInvestor']);
    Route::get('show',[\App\Http\Controllers\InvestorController::class,'showMyProfile']);

});




//Communication 
Route::prefix("communications")->group(function (){

  Route::post('/{id}', [\App\Http\Controllers\CommunicationController::class, 'store']);
});




});




