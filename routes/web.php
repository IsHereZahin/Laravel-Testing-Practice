<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
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
    return view('welcome');
});
Route::get('/download', [ProductController::class, 'download'])->name('download');

// Route::redirect('/', 'login');

Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware('is_admin')->group(function () {
        Route::get('product/create', [ProductController::class, 'create'])->name('product.create');
        Route::post('product/store', [ProductController::class, 'store'])->name('product.store');
        Route::get('product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
        Route::put('product/update/{id}', [ProductController::class, 'update'])->name('product.update');
        Route::delete('product/destroy/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    });
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
