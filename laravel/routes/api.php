<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

Route::post('/upload', [Controller::class, 'upload']);
Route::get('/history', [Controller::class, 'history']);  
