<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SavingsPlansController;
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
    ->name('subCategory.list');
Route::put('/subcategory/{id}/updateStatus', [SubCategoryController::class, 'updateSubcategoryStatus'])->name('subcategory.updateStatus');
Route::put('/subcategory/{id}/updateName', [SubCategoryController::class, 'updateSubCategoryName'])->name('subcategory.updateName');
Route::get('/subCategoriesList/{id}/new', [SubCategoryController::class, 'create'])
    ->name('subCategory.new');
Route::post('/subCategory/store', [SubCategoryController::class, 'store'])
    ->name('subCategory.store');


Route::get('/shoppingLists/index', [ShoppingListController::class, 'index'])->name('shopping-lists.index');
Route::post('/shoppingLists/{id}', [ShoppingListController::class, 'destroy'])->name('shopping-lists.destroy');
Route::get('/shoppingLists/{id}/edit', [ShoppingListController::class, 'edit'])->name('shopping-lists.edit');
Route::put('/shoppingLists/{id}', [ShoppingListController::class, 'update'])->name('shopping-lists.update');
Route::get('/shoppingLists/index/new', [ShoppingListController::class, 'create'])->name('shopping-lists.new');
Route::post('/shoppingLists/index/store', [ShoppingListController::class, 'store'])->name('shopping-lists.store');

Route::get('/savingsPlans/index', [SavingsPlansController::class, 'index'])->name('savings-plans.index');
Route::post('/savingsPlans/{id}', [SavingsPlansController::class, 'destroy'])->name('savings-plans.destroy');

