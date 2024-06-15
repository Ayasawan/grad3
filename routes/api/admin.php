<?php

use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('admin/login',[\App\Http\Controllers\PassportAuthController::class,'adminLogin'])->name('adminLogin');
// Route::get('request_otp', 'API\AuthController@requestOtp');
// Route::post('verify_otp', 'API\AuthController@verifyOtp');



// Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api','scopes:admin'] ],function() {
//     // authenticated staff routes here
//     //Route::get('dashboard',[PassportAuthController::class,'adminDashboard']);
//     Route::get('logout', [PassportAuthController::class, 'adminlogout'])->name('adminLogout');
//     Route::post('delete/{id}', [\App\Http\Controllers\PassportAuthController::class, 'destroy']);


//     //Complaint
//     Route::prefix("complaints")->group(function () {
//         Route::get('/', [\App\Http\Controllers\ComplaintController::class, 'index']);
//         Route::get('/{id}', [\App\Http\Controllers\ComplaintController::class, 'show']);
//         Route::post('delete/{id}', [\App\Http\Controllers\ComplaintController::class, 'destroyAdmin']);
//     });

//     Route::prefix("Interest")->group(function () {
//         Route::post('/', [\App\Http\Controllers\InterestController::class, 'store']);
//         Route::post('update/{id}', [\App\Http\Controllers\InterestController::class, 'update']);
//         Route::post('delete/{id}', [\App\Http\Controllers\InterestController::class, 'destroy']);
//     });
//     //Tracking
//     Route::prefix("trackings")->group(function () {

//         Route::get('/', [\App\Http\Controllers\TrackingController::class, 'index']);
//         Route::post('/', [\App\Http\Controllers\TrackingController::class, 'store']);
//         Route::get('/{id}', [\App\Http\Controllers\TrackingController::class, 'show']);
//         Route::post('update/{id}', [\App\Http\Controllers\TrackingController::class, 'update']);
//         Route::post('delete/{id}', [\App\Http\Controllers\TrackingController::class, 'destroy']);
//     });


//     //Investor
//     Route::prefix("investors")->group(function () {

//         Route::get('/', [\App\Http\Controllers\InvestorController::class, 'index']);
//         Route::get('showForAdmin/{id}', [\App\Http\Controllers\InvestorController::class, 'showForAdmin']);
//         Route::post('delete/{id}', [\App\Http\Controllers\InvestorController::class, 'destroyAdmin']);
//     });
//     //user

//         Route::get('/', [\App\Http\Controllers\UserController::class, 'indexUser']);
//         Route::get('showForAdmin/{id}', [\App\Http\Controllers\UserController::class, 'showForAdminUser']);
//         Route::post('delete/{id}', [\App\Http\Controllers\UserController::class, 'destroyAdminUser']);
//     });



//     Route::prefix("Article")->group(function () {
//         Route::post('/', [\App\Http\Controllers\ArticleController::class, 'store']);
//         Route::post('update/{id}', [\App\Http\Controllers\ArticleController::class, 'update']);
//         Route::post('delete/{id}', [\App\Http\Controllers\ArticleController::class, 'destroy']);
//     });

//     Route::prefix("reports")->group(function () {
//         Route::get('/', [ReportController::class, 'index']);
//         Route::get('/{id}', [\App\Http\Controllers\ReportController::class, 'show']);


//     });

//     Route::prefix("Type")->group(function () {
//         Route::post('/', [\App\Http\Controllers\TypeController::class, 'store']);
//         Route::post('update/{id}', [\App\Http\Controllers\TypeController::class, 'update']);
//         Route::post('delete/{id}', [\App\Http\Controllers\TypeController::class, 'destroy']);
//     });

//     Route::prefix("Transaction")->group(function () {
//         Route::get('/', [TransactionController::class, 'index']);
//         Route::get('/review-requests', [\App\Http\Controllers\TransactionController::class, 'reviewRequests']);
//         Route::get('/showAccepted', [\App\Http\Controllers\TransactionController::class, 'showAcceptedTransactions']);
//         Route::get('/{id}', [\App\Http\Controllers\TransactionController::class, 'show']);
//         Route::post('{id}/approve', [\App\Http\Controllers\TransactionController::class, 'approveTransaction']);
//         Route::post('/', [\App\Http\Controllers\TransactionController::class, 'store']);
//         Route::post('update/{id}', [\App\Http\Controllers\TransactionController::class, 'update']);
//         Route::post('delete/{id}', [\App\Http\Controllers\TransactionController::class, 'destroy']);
//     });
// });

Route::group(['middleware' => ['auth:investor-api,user-api,admin-api']], function () {



    Route::prefix("Article")->group(function (){
        Route::get('/',[\App\Http\Controllers\ArticleController::class,'index']);
        Route::get('/{id}',[\App\Http\Controllers\ArticleController::class,'show']);

    });

    Route::prefix("Type")->group(function (){
        Route::get('/{id}',[\App\Http\Controllers\TypeController::class,'show']);
        Route::get('/',[\App\Http\Controllers\TypeController::class,'index']);
        Route::get('/showProjectsByType/{id}',[\App\Http\Controllers\TypeController::class,'showProjectsByType']);

    });

    Route::prefix("Interest")->group(function (){
        Route::get('/',[\App\Http\Controllers\InterestController::class,'index']);
        Route::get('/{id}',[\App\Http\Controllers\InterestController::class,'show']);

    });

    Route::prefix("/{id}/Evaluation")->group(function (){
        Route::get('/', [EvaluationController::class, 'index']);
        Route::post('/', [EvaluationController::class, 'store']);
        Route::post('delete', [EvaluationController::class, 'destroy']);
        Route::get('all', [EvaluationController::class, 'getTotalEvaluationCount']);
    });


    Route::prefix("projects")->group(function (){

        Route::get('/{id}',[\App\Http\Controllers\ProjectController::class,'show']);
        Route::get('Name/{id}', [\App\Http\Controllers\ProjectController::class, 'searchByName']);
        Route::get('Amount/{id}', [\App\Http\Controllers\ProjectController::class, 'searchByAmount']);


    });

    Route::prefix("reports")->group(function (){
        Route::get('/{project_id}',[\App\Http\Controllers\ReportController::class,'projectReports']);
    });
});





Route::group(['middleware' => ['auth:investor-api,admin-api']], function () {

    //Project
    Route::prefix("projects")->group(function (){
        Route::get('/',[\App\Http\Controllers\ProjectController::class,'indexPublic']);
    });
});




Route::group(['middleware' => ['auth:investor-api,user-api']], function () {

    //Investor
    Route::prefix("investors")->group(function (){
        Route::get('/{id}',[\App\Http\Controllers\InvestorController::class,'showProfileByAnother']);
    });

});


Route::group(['middleware' => ['auth:user-api,admin-api']], function () {

    Route::prefix("projects")->group(function (){
        Route::post('delete/{id}',[\App\Http\Controllers\ProjectController::class,'destroy']);

    });
    Route::prefix("Transaction")->group(function (){
        Route::get('/{projectId}', [TransactionController::class, 'indexx']);

    });
});




Route::post('admin/login',[\App\Http\Controllers\PassportAuthController::class,'adminLogin'])->name('adminLogin');

Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api','scopes:admin'] ],function(){
    // authenticated staff routes here
    //Route::get('dashboard',[PassportAuthController::class,'adminDashboard']);
    Route::get('logout',[PassportAuthController::class,'adminlogout'])->name('adminLogout');
    Route::post('delete/{id}', [\App\Http\Controllers\PassportAuthController::class, 'destroy']);

    Route::post('update-bank-account-number', [\App\Http\Controllers\PassportAuthController::class, 'updateAdminBankAccountNumber']);


    //Complaint
    Route::prefix("complaints")->group(function (){
        Route::get('/',[\App\Http\Controllers\ComplaintController::class,'index']);
        Route::get('/{id}',[\App\Http\Controllers\ComplaintController::class,'show']);
        Route::post('delete/{id}',[\App\Http\Controllers\ComplaintController::class,'destroyAdmin']);
    });

    Route::prefix("Interest")->group(function (){
        Route::post('/',[\App\Http\Controllers\InterestController::class,'store']);
        Route::post('update/{id}',[\App\Http\Controllers\InterestController::class,'update']);
        Route::post('delete/{id}',[\App\Http\Controllers\InterestController::class,'destroy']);
    });
    //Tracking
    Route::prefix("trackings")->group(function (){

        Route::get('/',[\App\Http\Controllers\TrackingController::class,'index']);
        Route::post('/',[\App\Http\Controllers\TrackingController::class,'store']);
        Route::get('/{id}',[\App\Http\Controllers\TrackingController::class,'show']);
        Route::post('update/{id}',[\App\Http\Controllers\TrackingController::class,'update']);
        Route::post('delete/{id}',[\App\Http\Controllers\TrackingController::class,'destroy']);
    });



    //Investor
    Route::prefix("investors")->group(function (){

        Route::get('/',[\App\Http\Controllers\InvestorController::class,'index']);
        Route::get('showForAdmin/{id}',[\App\Http\Controllers\InvestorController::class,'showForAdmin']);
        Route::post('delete/{id}',[\App\Http\Controllers\InvestorController::class,'destroyAdmin']);
    });



    Route::prefix("Article")->group(function (){
        Route::post('/',[\App\Http\Controllers\ArticleController::class,'store']);
        Route::post('update/{id}',[\App\Http\Controllers\ArticleController::class,'update']);
        Route::post('delete/{id}',[\App\Http\Controllers\ArticleController::class,'destroy']);
    });

    Route::prefix("reports")->group(function (){
        Route::get('/', [ReportController::class, 'index']);
        Route::get('/{id}',[\App\Http\Controllers\ReportController::class,'show']);


    });

    Route::prefix("Type")->group(function (){
        Route::post('/',[\App\Http\Controllers\TypeController::class,'store']);
        Route::post('update/{id}',[\App\Http\Controllers\TypeController::class,'update']);
        Route::post('delete/{id}',[\App\Http\Controllers\TypeController::class,'destroy']);
    });

    Route::prefix("Transaction")->group(function (){
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/review-requests', [\App\Http\Controllers\TransactionController::class,'reviewRequests']);
        Route::get('/showAccepted', [\App\Http\Controllers\TransactionController::class,'showAcceptedTransactions']);
        Route::get('/{id}',[\App\Http\Controllers\TransactionController::class,'show']);
        Route::post('{id}/approve', [\App\Http\Controllers\TransactionController::class,'approveTransaction']);
        Route::post('/',[\App\Http\Controllers\TransactionController::class,'store']);
        Route::post('update/{id}',[\App\Http\Controllers\TransactionController::class,'update']);
        Route::post('delete/{id}',[\App\Http\Controllers\TransactionController::class,'destroy']);
    });


    //Project
    Route::prefix("projects")->group(function (){
        Route::get('/',[\App\Http\Controllers\ProjectController::class,'indexAdmin']);
        Route::get('acceptProject/{id}',[\App\Http\Controllers\ProjectController::class,'acceptProject']);
    });


    //Communication
    Route::prefix("communications")->group(function (){
        Route::get('/', [\App\Http\Controllers\CommunicationController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\CommunicationController::class, 'show']);
        Route::get('acceptRequest/{id}', [\App\Http\Controllers\CommunicationController::class, 'acceptRequest']);
    });

    Route::prefix("users")->group(function () {

        Route::get('/', [\App\Http\Controllers\UserController::class, 'indexUser']);
        Route::get('showForAdmin/{id}', [\App\Http\Controllers\UserController::class, 'showForAdminUser']);
        Route::post('delete/{id}', [\App\Http\Controllers\UserController::class, 'destroyAdmin']);
    });

});

