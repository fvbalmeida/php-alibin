<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::get('/link/{id}', [LinkController::class, 'getLink']);

Route::post('link/create', [LinkController::class, 'createLink']);

Route::delete('link/delete/{id}', [LinkController::class, 'deleteLink']);

Route::get('/', function () {
    return response()->json([
        'success' => true
    ]);
});
