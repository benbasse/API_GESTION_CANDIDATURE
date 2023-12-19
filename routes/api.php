<?php

use App\Http\Controllers\FormationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatureController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('formations/liste', [FormationController::class, 'index']);
Route::post('user/inscription', [AuthController::class, 'inscription']);
Route::get('user/listesAccepter', [AuthController::class, 'listesAccepter']);

Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:api', 'acces:admin'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('refuser/{id}', [AuthController::class, 'refuser']);
    Route::put('accepter/{id}', [AuthController::class, 'accepted']);
    Route::post('formations/store', [FormationController::class, 'store']);
    Route::put('formations/edit/{id}', [FormationController::class, 'update']);
    Route::delete('formations/{formation}', [FormationController::class, 'destroy']);
}); 
Route::middleware(['auth:api', 'acces:user'])->group(function () {
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/listesUser', [AuthController::class, 'index']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('candidater', [CandidatureController::class, 'candidater']);
    Route::get('user/listesAccepter', [AuthController::class, 'listesAccepter']);
    Route::get('user/listesNonAccepter', [AuthController::class, 'listesNonAccepter']);
    Route::get('user/listesNonAccepter', [AuthController::class, 'listesNonAccepter']);
});

