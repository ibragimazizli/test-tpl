<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

Route::post('/callService/{module}/{service}', [ApiController::class, 'callApi'])->middleware('api.security');

Route::get('/terminal/finish', function () { return response()->json(['message' => 'Terminal finished']); });
