<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NFeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



Route::post('auth/register', [AuthController::class, 'register']);

Route::post('auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', function(Request $request) {
        return auth()->user();
    });

    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::prefix('nfe')->group(function (){
        Route::get('status', [ NFeController::class, 'consultaStatus' ]);

        Route::get('/{chave}', [ NFeController::class, 'consultaDfe' ]);

        Route::post('cancela', [ NFeController::class, 'cancelaDfe' ]);

        Route::post('/', [ NFeController::class, 'gerarNFe' ]);
    });


});


