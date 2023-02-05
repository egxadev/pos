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

        // ROUTE RESOURCE PRODUCTS
        Route::resource('/products', \App\Http\Controllers\Apps\ProductController::class, ['as' => 'apps'])
            ->middleware('permission:products.index|products.create|products.edit|products.delete');

        // ROUTE RESOURCE CUSTOMERS
        Route::resource('/customers', \App\Http\Controllers\Apps\CustomerController::class, ['as' => 'apps'])
            ->middleware('permission:customers.index|customers.create|customers.edit|customers.delete');

        // ROUTE TRANSACTIONS
        Route::prefix('transactions')->group(function () {
            Route::get('/', [\App\Http\Controllers\Apps\TransactionController::class, 'index'])->name('apps.transactions.index');

            //route transaction searchProduct
            Route::post('/searchProduct', [\App\Http\Controllers\Apps\TransactionController::class, 'searchProduct'])->name('apps.transactions.searchProduct');

            //route transaction addToCart
            Route::post('/addToCart', [\App\Http\Controllers\Apps\TransactionController::class, 'addToCart'])->name('apps.transactions.addToCart');

            //route transaction destroyCart
            Route::post('/destroyCart', [\App\Http\Controllers\Apps\TransactionController::class, 'destroyCart'])->name('apps.transactions.destroyCart');

            //route transaction store
            Route::post('/store', [\App\Http\Controllers\Apps\TransactionController::class, 'store'])->name('apps.transactions.store');

            //route transaction print
            Route::get('/print', [\App\Http\Controllers\Apps\TransactionController::class, 'print'])->name('apps.transactions.print');
        });
    });
});
