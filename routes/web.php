<?php

use App\Http\Controllers\CollectionCardsController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\CollectionsEditListSearchController;
use App\Http\Controllers\CollectionsEditSearchController;
use App\Http\Controllers\CollectionsListController;
use App\Http\Controllers\CollectionsMoveController;
use App\Http\Controllers\FoldersController;
use App\Http\Controllers\FoldersMoveController;
use App\Http\Controllers\SetCollectionsController;
use App\Http\Controllers\SetCollectionsSearchController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Dashboard');
    })->name('dashboard');

    Route::get('collections/index', [CollectionsListController::class, 'index'])->name('collections-list.index');
    Route::patch('collections/move', [CollectionsMoveController::class, 'update'])->name('collections.move');
    Route::post('collections/{collection}/edit/search', [CollectionsEditSearchController::class, 'store'])->name('collection-edit-search.store');
    Route::post('collections/{collection}/edit/list-search', [CollectionsEditListSearchController::class, 'store'])->name('collection-edit-list-search.store');
    Route::post('collections/{collection}/edit/add', [CollectionCardsController::class, 'store'])->name('collection-cards.store');
    Route::resource('collections', CollectionsController::class);
    Route::patch('folders/move', [FoldersMoveController::class, 'update'])->name('folders.move');
    Route::resource('folders', FoldersController::class)->except('index');
    Route::resource('collection-set', SetCollectionsController::class)->only(['show', 'edit']);
    Route::get('collection-set-search', [SetCollectionsSearchController::class, 'index'])->name('collection-set-search.index');
});
