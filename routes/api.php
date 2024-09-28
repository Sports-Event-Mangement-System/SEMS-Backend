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
Route::post('/logout', [AuthController::class, 'logout'])->middleware( 'auth:sanctum' );
Route::get('/user/{id}', [AuthController::class, 'user'])->middleware( 'auth:sanctum' );
Route::post('/update/user/{id}', [AuthController::class, 'updateUser'])->middleware( 'auth:sanctum' );
Route::post('/update/profile_image/{id}', [AuthController::class, 'updateProfileImage'])->middleware( 'auth:sanctum' );
Route::delete('/delete/profile_image/{id}', [AuthController::class, 'deleteProfileImage'])->middleware( 'auth:sanctum' );
// Route::post('/update/password/{id}', [AuthController::class, 'updatePassword'])->middleware( 'auth:sanctum' );

/************************************************************** Contacts Form *************************************************************/
Route::get('/contacts', [ContactController::class, 'index'])->middleware( 'auth:sanctum' );
Route::get('/show/contacts/{id}', [ContactController::class, 'show'])->middleware( 'auth:sanctum' );
Route::post('/store/contacts', [ContactController::class, 'store']);
Route::delete('/delete/contacts/{id}', [ContactController::class, 'destroy'])->middleware( 'auth:sanctum' );

/************************************************************** Tournaments  *************************************************************/
Route::get('/tournaments', [TournamentController::class, 'index'])->middleware( 'auth:sanctum' );
Route::post('/store/tournaments', [TournamentController::class, 'store'])->middleware( 'auth:sanctum' );
Route::get('/edit/tournament/{id}', [TournamentController::class, 'edit'])->middleware( 'auth:sanctum' );
Route::post('/update/tournament/{id}', [TournamentController::class, 'update'])->middleware( 'auth:sanctum' );
Route::delete('/delete/tournament/{id}', [TournamentController::class, 'destroy'])->middleware( 'auth:sanctum' );
Route::post('/update-status/tournament/{id}', [TournamentController::class, 'updateStatus'])->middleware( 'auth:sanctum' );
//To fetch specific tournaments data for users
Route::get('show/tournament/{id}', [TournamentController::class, 'show']);
//To fetch active status tournaments.
Route::get('active/tournaments', [TournamentController::class, 'activeTournaments']);

