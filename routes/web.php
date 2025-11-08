<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RatingController;
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
// Route::middleware('auth')->group(function () {
//     Route::get('/ratings/create', [RatingController::class, 'create'])->name('rating.create');
//     Route::post('/ratings/store', [RatingController::class, 'store'])->name('rating.store');
// });



Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Route::resource('authors', AuthorController::class)->only(['index', 'show']);
Route::get('/authors/top', [AuthorController::class, 'top'])->name('authors.top');

Route::get('/ratings/create', [RatingController::class, 'create'])->name('ratings.create');
Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
