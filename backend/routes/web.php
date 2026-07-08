<?php

use App\Models\TagRead;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('rfid-live', ['existing' => TagRead::latest()->take(10)->get()]));
