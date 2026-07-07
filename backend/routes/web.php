<?php

use App\Models\TagRead;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/live');
Route::get('/live', fn () => view('rfid-live', ['existing' => TagRead::latest()->take(10)->get()]));
