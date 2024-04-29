<?php

use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\PassportAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['auth:investor-api,user-api,admin-api']], function () {

    Route::prefix("Article")->group(function (){
        Route::get('/',[\App\Http\Controllers\ArticleController::class,'index']);
        Route::get('/{id}',[\App\Http\Controllers\ArticleController::class,'show']);
    });

    Route::prefix("Type")->group(function (){
        Route::get('/{id}',[\App\Http\Controllers\TypeController::class,'show']);
        Route::get('/',[\App\Http\Controllers\TypeController::class,'index']);
    });


    Route::prefix("/{id}/Evaluation")->group(function (){
              Route::get('/', [EvaluationController::class, 'index']);
              Route::post('/', [EvaluationController::class, 'store']);
              Route::post('delete', [EvaluationController::class, 'destroy']);
    });

    
    Route::prefix("projects")->group(function (){
        Route::get('/',[\App\Http\Controllers\ProjectController::class,'index']);
        Route::get('/{id}',[\App\Http\Controllers\ProjectController::class,'show']);
        
    });

        //Investor
        Route::prefix("investors")->group(function (){
            Route::get('/{id}',[\App\Http\Controllers\InvestorController::class,'show']);
        });

});





Route::group(['middleware' => ['auth:user-api,admin-api']], function () {

    Route::prefix("projects")->group(function (){
        Route::post('delete/{id}',[\App\Http\Controllers\ProjectController::class,'destroy']);
        
    });
});




Route::post('admin/login',[\App\Http\Controllers\PassportAuthController::class,'adminLogin'])->name('adminLogin');

Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api','scopes:admin'] ],function(){
   // authenticated staff routes here
    //Route::get('dashboard',[PassportAuthController::class,'adminDashboard']);
    Route::get('logout',[PassportAuthController::class,'adminlogout'])->name('adminLogout');
    Route::post('delete/{id}', [\App\Http\Controllers\PassportAuthController::class, 'destroy']);



        //Complaint
        Route::prefix("complaints")->group(function (){
            Route::get('/',[\App\Http\Controllers\ComplaintController::class,'index']);
            Route::get('/{id}',[\App\Http\Controllers\ComplaintController::class,'show']);
            Route::post('delete/{id}',[\App\Http\Controllers\ComplaintController::class,'destroyAdmin']);
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
            Route::get('/{id}',[\App\Http\Controllers\InvestorController::class,'show']);
            Route::post('delete/{id}',[\App\Http\Controllers\InvestorController::class,'destroyAdmin']);
        });



        Route::prefix("Article")->group(function (){
            Route::post('/',[\App\Http\Controllers\ArticleController::class,'store']);
            Route::post('update/{id}',[\App\Http\Controllers\ArticleController::class,'update']);
            Route::post('delete/{id}',[\App\Http\Controllers\ArticleController::class,'destroy']);
        });
      

        Route::prefix("Type")->group(function (){
            Route::post('/',[\App\Http\Controllers\TypeController::class,'store']);
            Route::post('update/{id}',[\App\Http\Controllers\TypeController::class,'update']);
            Route::post('delete/{id}',[\App\Http\Controllers\TypeController::class,'destroy']);
        });

    });
