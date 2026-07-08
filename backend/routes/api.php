<?php

use App\Http\Controllers\RfidScanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/rfid-scans',  [RfidScanController::class, 'index']);
