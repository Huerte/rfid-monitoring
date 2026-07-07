<?php

use App\Models\TagRead;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('dashboard'));
Route::get('/dashboard', fn () => view('dashboard'));
Route::redirect('/', '/api/rfid-scans');
