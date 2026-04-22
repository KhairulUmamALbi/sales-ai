<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesPageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // return redirect()->route('pages.index');
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/dashboard', function () {
//     return redirect()->route('pages.index');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


        // Sales Pages
    Route::get('/pages', [SalesPageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [SalesPageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [SalesPageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}', [SalesPageController::class, 'show'])->name('pages.show');
    Route::get('/pages/{page}/edit', [SalesPageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [SalesPageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [SalesPageController::class, 'destroy'])->name('pages.destroy');

    // Bonus features
    Route::post('/pages/{page}/regenerate-section', [SalesPageController::class, 'regenerateSection'])
        ->name('pages.regenerate-section');
    Route::get('/pages/{page}/export', [SalesPageController::class, 'exportHtml'])
        ->name('pages.export');



});

require __DIR__.'/auth.php';
