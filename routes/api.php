<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TournamentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user/{id}', [AuthController::class, 'user'])->middleware('auth:sanctum' );
Route::post('/update/user/{id}', [AuthController::class, 'updateUser'])->middleware('auth:sanctum');

/************************************************************** Contacts Form *************************************************************/
Route::post('/store/contacts', [ContactController::class, 'store']);
// Route::post('/delete/contacts', [AuthController::class, 'destroy']);

/************************************************************** Tournaments  *************************************************************/
Route::get('/tournaments', [TournamentController::class, 'index']);
Route::post('/store/tournaments', [TournamentController::class, 'store'])->middleware('auth:sanctum' );
