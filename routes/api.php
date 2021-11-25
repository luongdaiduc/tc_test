<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParcelController;
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

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::group(['prefix'=>'parcels'], function(){
        Route::post('/', [ParcelController::class, 'create'])->name('parcel_create');
        Route::put('/{parcel}', [ParcelController::class, 'update'])->name('parcel_update');
        Route::get('/{id}', [ParcelController::class, 'show'])->name('parcel_detail');
        Route::delete('/{parcel}', [ParcelController::class, 'delete'])->name('parcel_delete');
    });
});
