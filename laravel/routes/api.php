<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

Route::post('/upload', [Controller::class, 'upload']);
