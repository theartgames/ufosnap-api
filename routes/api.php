<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthorizeApiKey;
use App\Http\Controllers\PdfController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('/', function (Request $request) {
    return 'opa';
});

Route::middleware([AuthorizeApiKey::class])->group(function () {
    Route::get('/pdf', [PdfController::class, 'getPdfFromUrl']);
    Route::post('/pdf', [PdfController::class, 'getPdfFromBody']);
});
