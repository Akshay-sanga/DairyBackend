<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Optimization#############************************
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');

    // Check if the command ran successfully
    if ($exitCode === 0) {
        return 'Optimization completed successfully.';
    } else {
        return 'An error occurred while optimizing.';
    }
});
Route::get('/storage-link', [AdminController::class, 'storage_link']);

Route::get('/', function () {
    return view('welcome');
});
