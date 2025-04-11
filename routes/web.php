<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;


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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/home', function () {
    return view('home');
});

// Route::get('/map', function () {
//     $locations = Location::all(); // Ambil semua lokasi dari database
//     return view('map', compact('locations'));
// })->name('map')->middleware('auth');

// Arahkan /map ke halaman CRUD lokasi
Route::redirect('/map', '/locations')->name('map');

// CRUD lokasi
Route::resource('locations', LocationController::class)->middleware('auth');

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::resource('locations', LocationController::class);
});
