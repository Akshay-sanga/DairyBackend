<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MilkCollectionController;
use App\Http\Controllers\MilkRateController;
use App\Http\Controllers\SnfChartController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductMasterController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\SnfFormulaController;
use App\Http\Controllers\DailyMilkSaleController;
use App\Http\Controllers\MilkDispatchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Admin API Routes
Route::post('/create-admin', [AdminController::class, 'create']);
Route::post('/verify-otp', [AdminController::class, 'verifyOtp']);

Route::post('/send-otp-forget-password', [AdminController::class, 'SendForgetOtp']);
Route::post('/forget-password', [AdminController::class, 'ForgetPassword']);

Route::post('/login-admin', [AdminController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    ///////////////////////////////customer//////////////////////////
Route::post('/customer-submit', [CustomerController::class,'submit']);
Route::get('/all-customer', [CustomerController::class,'all']);
Route::get('/edit-customer/{id}', [CustomerController::class,'edit']);
Route::post('/update-customer/{id}', [CustomerController::class,'update']);
Route::post('/delete-customer/{id}', [CustomerController::class,'delete']);
Route::post('/update-status-customer/{id}', [CustomerController::class,'UpdateStatus']);

///////////////////milk colletion//////////////////////////////////

Route::post('/milk-collection-submit', [MilkCollectionController::class,'submit']);
Route::get('/all-milk-collection', [MilkCollectionController::class,'all']);
Route::get('/edit-milk-collection/{id}', [MilkCollectionController::class,'edit']);
Route::post('/update-milk-collection/{id}', [MilkCollectionController::class,'update']);
Route::post('/delete-milk-collection/{id}', [MilkCollectionController::class,'delete']);


///////////////////Product Category//////////////////////////////////

Route::post('/product-category-submit', [ProductCategoryController::class,'submit']);
Route::get('/all-product-category', [ProductCategoryController::class,'all']);
Route::get('/edit-product-category/{id}', [ProductCategoryController::class,'edit']);
Route::post('/update-product-category/{id}', [ProductCategoryController::class,'update']);
Route::post('/delete-product-category/{id}', [ProductCategoryController::class,'delete']);
Route::post('/update-status-category/{id}', [ProductCategoryController::class,'UpdateStatus']);

///////////////////Product Master//////////////////////////////////

Route::post('/product-submit', [ProductMasterController::class,'submit']);
Route::get('/all-product', [ProductMasterController::class,'all']);
Route::get('/edit-product/{id}', [ProductMasterController::class,'edit']);
Route::post('/update-product/{id}', [ProductMasterController::class,'update']);
Route::post('/delete-product/{id}', [ProductMasterController::class,'delete']);
Route::post('/update-status-product/{id}', [ProductMasterController::class,'UpdateStatus']);

///////////////////Product Stock//////////////////////////////////

Route::post('/product-stock-submit', [ProductStockController::class,'submit']);
Route::get('/all-product-stock', [ProductStockController::class,'all']);
Route::get('/edit-product-stock/{id}', [ProductStockController::class,'edit']);
Route::post('/update-product-stock/{id}', [ProductStockController::class,'update']);
Route::post('/delete-product-stock/{id}', [ProductStockController::class,'delete']);
Route::post('/update-status-product-stock/{id}', [ProductStockController::class,'UpdateStatus']);

///////////////////Daily Milk Sale//////////////////////////////////

Route::post('/daily-milk-sale-submit', [DailyMilkSaleController::class,'submit']);
Route::get('/all-daily-milk-sale', [DailyMilkSaleController::class,'all']);
Route::get('/edit-daily-milk-sale/{id}', [DailyMilkSaleController::class,'edit']);
Route::post('/update-daily-milk-sale/{id}', [DailyMilkSaleController::class,'update']);
Route::post('/delete-daily-milk-sale/{id}', [DailyMilkSaleController::class,'delete']);

///////////////////Milk Dispatch//////////////////////////////////

Route::post('/milk-dispatch-submit', [MilkDispatchController::class,'submit']);
Route::get('/all-milk-dispatch', [MilkDispatchController::class,'all']);
Route::get('/edit-milk-dispatch/{id}', [MilkDispatchController::class,'edit']);
Route::post('/update-milk-dispatch/{id}', [MilkDispatchController::class,'update']);
Route::post('/delete-milk-dispatch/{id}', [MilkDispatchController::class,'delete']);




///////////////////milk Rates//////////////////////////////////

Route::get('/milk-rates', [MilkRateController::class,'index']);
Route::post('/milk-rates-submit', [MilkRateController::class,'store']);
// Route::get('/all-milk-rates', [MilkRateController::class,'show']);
Route::post('/update-milk-rates/{id}', [MilkRateController::class,'update']);
Route::post('/delete-milk-rates/{id}', [MilkRateController::class,'destroy']);

Route::post('milk-rates/import', [MilkRateController::class, 'importFile']);
Route::get('/export-demo-both', [MilkRateController::class, 'exportDemoBoth']);



//////////////////////SNF Chart/////////////////////////////////////////////////

Route::post('/snf-chart/save', [SnfChartController::class, 'store']);

//////////////////////SNF FORMULA/////////////////////////////////////////////////

Route::post('/snf-formula/save', [SnfFormulaController::class, 'store']);
Route::get('/snf-formula/latest', [SnfFormulaController::class, 'getLatest']);


///////////////////////////Logout///////////////////////////////////////////////////////
Route::post('/logout', [AdminController::class,'logout']);
});