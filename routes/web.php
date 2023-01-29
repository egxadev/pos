<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ROUTE HOME
Route::get('/', function () {
    return \Inertia\Inertia::render('Auth/Login');
})->middleware('guest');

// PREFIX "APPS"
Route::prefix('apps')->group(function () {
    // MIDDLEWARE "AUTH"
    Route::group(['middleware' => ['auth']], function () {
        // ROUTE DASHBOARD
        Route::get('dashboard', App\Http\Controllers\Apps\DashboardController::class)
            ->name('apps.dashboard');

        // ROUTE PERMISSIONS
        Route::get('/permissions', \App\Http\Controllers\Apps\PermissionController::class)
            ->name('apps.permissions.index')
            ->middleware('permission:permissions.index');

        // ROUTE RESOURCE ROLES
        Route::resource('/roles', \App\Http\Controllers\Apps\RoleController::class, ['as' => 'apps'])
            ->middleware('permission:roles.index|roles.create|roles.edit|roles.destroy');

        // ROUTE RESOURCE USERS
        Route::resource('/users', \App\Http\Controllers\Apps\UserController::class, ['as' => 'apps'])
            ->middleware('permission:users.index|users.create|users.edit|users.delete');

        // ROUTE RESOURCE CATEGORIES
        Route::resource('/categories', \App\Http\Controllers\Apps\CategoryController::class, ['as' => 'apps'])
            ->middleware('permission:categories.index|categories.create|categories.edit|categories.delete');
    });
});
