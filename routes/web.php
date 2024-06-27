<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;

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

Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    return 'Cache cleared successfully.';
});

Route::get('/config-clear', function () {
    Artisan::call('config:clear');
    return 'Configuration cache cleared successfully.';
});

Route::get('/route-clear', function () {
    Artisan::call('route:clear');
    return 'Route cache cleared successfully.';
});

Route::get('/view-clear', function () {
    Artisan::call('view:clear');
    return 'View cache cleared successfully.';
});

Route::get('/migrate-fresh', function () {

    Artisan::call('migrate:fresh',['--seed' => true,'--force' => true]);

    return "done";

});
Route::get('/create-client1', function () {

    Artisan::call('passport:client',[
        '--no-interaction'=>true,
        '--name'=>'Tenant Password Grant Client'
            ]);
    return "done";

});


// Route::get('/create-client', [PassportAuthController::class, 'createClient']);