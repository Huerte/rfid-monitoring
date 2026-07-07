<?php

use App\Models\TagRead;
use Illuminate\Support\Facades\Route;

<<<<<<< Updated upstream
Route::get('/', fn () => view('dashboard'));
Route::get('/dashboard', fn () => view('dashboard'));
=======
Route::redirect('/', '/api/rfid-scans');
Route::get('/live', fn() => view('rfid-live', ['existing' => TagRead::latest()->take(10)->get()]));
>>>>>>> Stashed changes
