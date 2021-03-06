<?php

use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardsSearchController;
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
use App\Http\Controllers\GroupsSearchController;
use App\Http\Controllers\GroupUsersController;
use App\Http\Controllers\SetCollectionsController;
use App\Http\Controllers\SetCollectionsSearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StoresController;
use App\Http\Controllers\SymbolsController;
use App\Http\Controllers\UsersController;
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

    Route::get('cards/{card}', [CardController::class, 'show'])->name('cards/show');
    Route::post('cards-search', [CardsSearchController::class, 'store'])->name('cards-search.store');
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
    Route::resource('folders', FoldersController::class)->except('index', 'edit');
    Route::get('group/search', [GroupsSearchController::class, 'show'])->name('groups.search.show');
    Route::post('group/search', [GroupsSearchController::class, 'store'])->name('groups.search.store');
    Route::get('group/user/{user}', [GroupUsersController::class, 'show'])->name('groups.users.show');
    Route::get('group/{uuid}', [GroupsController::class, 'show'])->name('groups.show');
    Route::get('group', [GroupsController::class, 'index'])->name('groups.index');
    Route::resource('collections', CollectionsController::class)->only(['index', 'create', 'store']);
    Route::middleware('isPublic')->group(function () {
        Route::resource('collections', CollectionsController::class)->except(['index', 'create', 'store']);
    });
    Route::get('user/settings', [SettingsController::class, 'show'])->name('settings.show');
    Route::patch('user/update-settings', [SettingsController::class, 'update'])->name('settings.update-settings');

    Route::post('api/brace-content', [SymbolsController::class, 'show'])->name('symbols.show');

    // Admin Routes
    Route::get('admin-panel/edit', [AdminPanelController::class, 'edit'])->name('admin-panel.edit');
    Route::middleware(['isInAdminPanel'])->group(function () {
        Route::get('stores', [StoresController::class, 'index'])->name('stores.index');
        Route::get('stores/create', [StoresController::class, 'create'])->name('stores.create');
        Route::post('stores', [StoresController::class, 'store'])->name('stores.store');
        Route::get('users', [UsersController::class, 'index'])->name('users.index');
    });
});
