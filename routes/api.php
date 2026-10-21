<?php

use App\Http\Controllers\Api\ApiArticleController;
use App\Http\Controllers\Api\ApiSeedController;
use App\Http\Controllers\Api\ApiSubmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Seeds API
    Route::get('/seeds', [ApiSeedController::class, 'index']);
    Route::get('/seeds/categories', [ApiSeedController::class, 'categories']);
    Route::get('/seeds/{slug}', [ApiSeedController::class, 'show']);

    // Articles API
    Route::get('/articles', [ApiArticleController::class, 'index']);
    Route::get('/articles/{slug}', [ApiArticleController::class, 'show']);

    // Submit Seed API
    Route::post('/contribute-seed', [ApiSubmissionController::class, 'store']);

    // General Stats API
    Route::get('/stats', [ApiSeedController::class, 'stats']);
});
