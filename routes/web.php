<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SavingsPlansController;
use App\Http\Controllers\TransactionController;
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
Route::post('/savingsPlans/delete/{id}', [SavingsPlansController::class, 'destroy'])->name('savings-plans.destroy');
Route::get('/savingsPlans/index/new', [SavingsPlansController::class, 'create'])->name('savings-plans.new');
Route::post('savingsPlans/store', [SavingsPlansController::class, 'store'])->name('savings-plans.store');
Route::get('/savingsPlans/{id}/edit', [SavingsPlansController::class, 'edit'])->name('savings-plans.edit');
Route::put('/savingsPlans/{id}/update', [SavingsPlansController::class, 'update'])->name('savings-plans.update');
Route::post('/savingsPlans/{id}/updateAmount', [SavingsPlansController::class, 'updateAmount'])->name('savings-plans.update-amount');


Route::get('/transactions/index', [TransactionController::class, 'index'])->name('transactions.index');
Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
Route::post('/transactions/store', [TransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
Route::put('/transactions/{transaction}/update', [TransactionController::class, 'update'])->name('transactions.update');
Route::post('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
Route::get('/transactions/generate/prediction', [TransactionController::class, 'generatePrediction'] )->name('generate.prediction');


Route::get('/generate/report/year', [ReportController::class, 'generateYearlyReport'] )->name('generate.yearly.report');
Route::get('/generate/report/year/pdf', [ReportController::class, 'yearlyReportPDF'] )->name('generate.yearly.report.pdf');
Route::get('/generate/report/month', [ReportController::class, 'generateMonthlyReport'] )->name('generate.monthly.report');
Route::get('/generate/report/month/pdf', [ReportController::class, 'monthlyReportPDF'] )->name('generate.monthly.report.pdf');
Route::get('/generate/report/week', [ReportController::class, 'generateWeeklyReport'] )->name('generate.weekly.report');
Route::get('/generate/report/week/pdf', [ReportController::class, 'WeeklyReportPDF'] )->name('generate.weekly.report.pdf');

Route::get('/ranking', [RankingController::class, 'index'] )->name('ranking.index');
