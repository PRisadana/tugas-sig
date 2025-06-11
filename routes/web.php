<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\PolygonController;
use App\Http\Controllers\Auth\ExternalAuthController;
use App\Http\Controllers\RuasJalanController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::get('/home', function () {
//     return view('home');
// });

// Route::get('/map', function () {
//     $locations = Location::all(); // Ambil semua lokasi dari database
//     return view('map', compact('locations'));
// })->name('map')->middleware('auth');



// // CRUD lokasi
// Route::resource('locations', LocationController::class)->middleware('auth');

// require __DIR__.'/auth.php';

Route::get('/login', [ExternalAuthController::class, 'loginForm'])->name('login');
Route::post('/login', [ExternalAuthController::class, 'login']);

Route::get('/register', [ExternalAuthController::class, 'registerForm'])->name('register');
Route::post('/register', [ExternalAuthController::class, 'register']);

Route::post('/logout', [ExternalAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::resource('locations', LocationController::class)->names([
        'index' => 'locations.index',
        'create' => 'locations.create',
        'store' => 'locations.store',
        'show' => 'locations.show',
        'edit' => 'locations.edit',
        'update' => 'locations.update',
        'destroy' => 'locations.destroy',
    ]);
    Route::resource('points', PointController::class); // Default names (e.g., points.index)
    Route::resource('lines', LineController::class);   // Default names (e.g., lines.index)
    Route::resource('polygons', PolygonController::class); // Default names (e.g., polygons.index)
});

// Arahkan /map ke halaman CRUD lokasi
Route::redirect('/map', '/locations')->name('map');

// Route::redirect('/dashboard', '/locations')->name('locations');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/provinsi', [\App\Http\Controllers\WilayahController::class, 'getProvinsi'])->middleware('auth');
Route::get('/wilayah/semua', [\App\Http\Controllers\WilayahController::class, 'semuaWilayah'])
    ->middleware('auth')
    ->name('wilayah.semua');

Route::get('/wilayah/form', function () {
    return view('wilayah.form');
})->middleware('auth');

// Route::middleware(['auth'])->group(function () {
//     Route::get('/ruasjalan', [\App\Http\Controllers\RuasJalanController::class, 'index'])->name('ruasjalan.index');
//     Route::get('/ruasjalan/create', [\App\Http\Controllers\RuasJalanController::class, 'create'])->name('ruasjalan.create');
// });

Route::resource('ruasjalan',RuasJalanController::class)->middleware('auth');

// Edit form
Route::get('/ruasjalan/{id}/edit', [RuasJalanController::class, 'edit'])->name('ruasjalan.edit');

// Update action
Route::put('/ruasjalan/{id}', [RuasJalanController::class, 'update'])->name('ruasjalan.update');






