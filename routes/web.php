<?php

use App\Http\Controllers\CollectionCardsController;
use App\Http\Controllers\CollectionCardsDeleteController;
use App\Http\Controllers\CollectionCardsMoveController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\CollectionsEditListSearchController;
use App\Http\Controllers\CollectionsEditSearchController;
use App\Http\Controllers\CollectionsListController;
use App\Http\Controllers\CollectionsMoveController;
use App\Http\Controllers\CollectionsSearchController;
use App\Http\Controllers\FoldersController;
use App\Http\Controllers\FoldersMoveController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\GroupUsersController;
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
        // return Inertia::render('Dashboard/Dashboard');
        return redirect(route('collections.index'));
    })->name('dashboard');

    Route::get('collections/index', [CollectionsListController::class, 'index'])->name('collections-list.index');
    Route::patch('collections/move', [CollectionsMoveController::class, 'update'])->name('collections-move.update');
    Route::post('collections/{collection}/cards/move', [CollectionCardsMoveController::class, 'update'])->name('collection-cards-move.update');
    Route::post('collections/{collection}/cards/delete', [CollectionCardsDeleteController::class, 'update'])->name('collection-cards-delete.update');
    Route::post('collections/{collection}/edit/search', [CollectionsEditSearchController::class, 'store'])->name('collection-edit-search.store');
    Route::post('collections/{collection}/edit/list-search', [CollectionsEditListSearchController::class, 'store'])->name('collection-edit-list-search.store');
    Route::post('collections/{collection}/edit/add', [CollectionCardsController::class, 'store'])->name('collection-cards.store');
    Route::resource('collection-set', SetCollectionsController::class)->only(['show', 'edit']);
    Route::get('collections-search', [CollectionsSearchController::class, 'show'])->name('collections-search.show');
    Route::post('collections-search', [CollectionsSearchController::class, 'store'])->name('collections-search.store');
    Route::get('collection-set-search', [SetCollectionsSearchController::class, 'index'])->name('collection-set-search.index');
    Route::patch('folders/move', [FoldersMoveController::class, 'update'])->name('folders.move');
    Route::resource('folders', FoldersController::class)->except('index');
    Route::get('group/user/{user}', [GroupUsersController::class, 'show'])->name('group-users.show');
    Route::get('group/{uuid}', [GroupsController::class, 'show'])->name('groups.show');
    Route::get('group', [GroupsController::class, 'index'])->name('groups.index');
    // Route::resource('collections', CollectionsController::class);
    Route::middleware('isPublic')->group(function () {
        Route::resource('collections', CollectionsController::class)->except(['index']);
    });

    Route::resource('collections', CollectionsController::class)->only(['index']);
});
