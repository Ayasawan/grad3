<?php


use App\Http\Controllers\InterestController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ChatController;

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



Route::post('investor/register', [PassportAuthController::class, 'registerInvestor'])->name('registerInvestor');
Route::post('investor/login', [PassportAuthController::class, 'LoginInvestor'])->name('LoginInvestor');
Route::post('verify_otpInv',[\App\Http\Controllers\PassportAuthController::class,'verifyOtpInv']);

Route::group( ['prefix' =>'investor','middleware' => ['auth:investor-api','scopes:investor'] ],function(){
    // authenticated staff routes here

//    Route::get('dashboard',[PassportAuthController::class, 'userDashboard']);
    Route::get('logout',[PassportAuthController::class,'LogoutInvestor'])->name('LogoutInvestor');

    Route::get('/{project_id}/reports', [ReportController::class, 'showReportsFor_investor']);
    Route::post('/interests', [InterestController::class, 'addInterests']);
    Route::get('/projects/investor-interests', [InterestController::class, 'getProjectsByInvestorInterests']);


//Complaint
    Route::prefix("complaints")->group(function (){

        Route::post('/',[\App\Http\Controllers\ComplaintController::class,'store']);
        Route::post('update/{id}',[\App\Http\Controllers\ComplaintController::class,'update']);
        Route::post('delete/{id}',[\App\Http\Controllers\ComplaintController::class,'destroyInvestor']);
        Route::get('getInvestorComplaints/{id}',[\App\Http\Controllers\ComplaintController::class,'getInvestorComplaints']);
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

    //messages
    Route::post('/sendMessageInvestor', [\App\Http\Controllers\ChatController::class, 'sendMessageInvestor']);
    Route::post('/indexInvestor', [\App\Http\Controllers\ChatController::class, 'indexInvestor']);
    Route::get('/admins-with-unseen-messages', [\App\Http\Controllers\ChatController::class, 'adminWithUnseenMessages']);





});







Route::get('/2', function () {
  
    return "abeer ok";
});


Route::get('/investor1', function () {

    $investor = Investor::all();

    return $investor;

});