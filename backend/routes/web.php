<?php

use App\Models\TagRead;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('rfid-live', ['existing' => TagRead::orderBy('updated_at', 'desc')->take(10)->get()]));
