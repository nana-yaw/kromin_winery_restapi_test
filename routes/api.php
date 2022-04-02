<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailVerificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WineController;
use \App\Models\Wine;

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

Route::group(['prefix' => 'auth'], function () {

    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('signup', [AuthController::class, 'signup'])->name('signup');
    Route::get('signup/activate/{token}', [AuthController::class, 'signupActivate'])->name('signupActivate');

    Route::group(['middleware' => 'auth:api'], function() {

        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('user', [AuthController::class, 'user'])->name('user');
        Route::delete('user/{user}', [AuthController::class, 'destroyUser'])->name('user.delete');

        Route::group(['middleware' => 'scope:admin'], function() {

            Route::post('admin-signup', [AuthController::class, 'signupByAdmin'])->name('admin.signup');

        });

    });
});


Route::group(['prefix' => 'password_recovery'], function () {

    Route::post('create', [PasswordResetController::class, 'create'])->name('password.request');
    Route::get('find', [PasswordResetController::class, 'find'])->name('password.reset');
    Route::post('reset', [PasswordResetController::class, 'reset'])->name('password.update');

});


Route::group(['middleware' => ['auth:api', 'scope:admin']], function() {

        Route::put('users/{id}/role', [RoleController::class, 'changeRole'])->name('role.change');
        Route::get('users/{id}/role', [RoleController::class, 'getRole'])->name('role.get');

});


Route::group(['middleware' => ['auth:api', 'scope:admin']], function() {

    Route::post('wines', [WineController::class, 'store'])->name('wines.store');
    Route::put('wines/{wine}', [WineController::class, 'update'])->name('wines.update');
    Route::delete('wines/{wine}', [WineController::class, 'destroy'])->name('wines.destroy');

});

Route::group(['middleware' => 'auth:api'], function() {
    Route::get('wines', [WineController::class, 'index'])->name('wines.index');
    Route::get('wines/{uuid}', [WineController::class, 'show'])->name('wines.show');
});

Route::group(['middleware' => 'auth:api'], function() {

    Route::get('photos', [PhotoController::class, 'index'])->name('photo.index');

    Route::group(['middleware' => 'scope:admin'], function() {
        Route::post('photos', [PhotoController::class, 'store'])->name('photo.store');
        Route::delete('photos/{photo}', [PhotoController::class, 'destroy'])->name('photo.destroy');
    });
});
