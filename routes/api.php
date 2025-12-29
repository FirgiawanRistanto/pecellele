<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReportController; // Import ReportController

Route::apiResource('menus', MenuController::class);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/reports/pdf', [ReportController::class, 'generatePdf']); // New route for PDF reports

