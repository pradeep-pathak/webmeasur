<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    if (Auth::check()) {
        return redirect("/sites");
    } else {
        return redirect('/login');
    }
});

Route::get('/sites', [DashboardController::class, "index"])->middleware(['auth', 'verified'])->name('sites');

Route::get('/sites/{domain}/{publicCode?}', [DashboardController::class, "show"])->name('sites.show');

Route::post('/sites', [SiteController::class, "store"])->middleware(['auth', 'verified'])->name('sites.store');
Route::delete('/sites/{domain}', [SiteController::class, "destroy"])->middleware(['auth', 'verified'])->name('sites.destroy');
Route::patch('/sites/{domain}/change-access', [SiteController::class, "changeAccess"])->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/my-account', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/my-account', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/my-account', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';