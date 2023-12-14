<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\SubCategoryController;
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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/categoriesList', [CategoryController::class, 'list'])->name('category.list');
Route::put('/categoryUpdate/{id}', [CategoryController::class, 'update']);
Route::get('/categories/create', [CategoryController::class, 'create'])->name('create.category');
Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
Route::get('/archive/category/{id}', [CategoryController::class, 'archive'])->name('archive.category');
Route::get('/category/archive', [CategoryController::class, 'archiveList'])->name('category.archiveList');
Route::get('/category/restore/{id}', [CategoryController::class, 'restore'])->name('category.restore');


Route::get('/subCategoriesList/{id}', [SubCategoryController::class, 'list'])
    ->name('subCategory.list')
    ->middleware('verify.category.access');
Route::put('/subcategory/{id}/updateStatus', [SubCategoryController::class, 'updateSubcategoryStatus'])->name('subcategory.updateStatus');
Route::put('/subcategory/{id}/updateName', [SubCategoryController::class, 'updateSubCategoryName'])->name('subcategory.updateName');
Route::get('/subCategoriesList/{id}/new', [SubCategoryController::class, 'create'])
    ->name('subCategory.new')
    ->middleware('verify.category.access');
Route::post('/subCategory/store', [SubCategoryController::class, 'store'])
    ->name('subCategory.store');


Route::get('/shoppingLists/index', [ShoppingListController::class, 'index'])->name('shopping-lists.index');
Route::get('/shoppingLists/{id}/edit', [ShoppingListController::class, 'edit'])->name('shopping-lists.edit');
Route::post('/shoppingLists/{id}', [ShoppingListController::class, 'destroy'])->name('shopping-lists.destroy');
