<?php

use App\Http\Controllers\QuestionnaireController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/questionnaire', [QuestionnaireController::class, 'get']);
