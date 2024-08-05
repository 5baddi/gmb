<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

use Illuminate\Support\Facades\Route;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\ScheduledPostsController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\EditScheduledPostsController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\SaveScheduledPostController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\DeleteScheduledPostController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\UploadScheduledPostMediaController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\DeleteScheduledPostMediaController;

Route::middleware(['auth'])
    ->name('dashboard.scheduled-posts')
    ->prefix('dashboard/scheduled-posts')
    ->group(function() {
        Route::get('/', ScheduledPostsController::class);
        Route::post('/upload/{id}', UploadScheduledPostMediaController::class)->name('.upload.media');
        Route::delete('/delete/{id}', DeleteScheduledPostMediaController::class)->name('.delete.media');

        Route::get('/{type}/{id?}', EditScheduledPostsController::class)->name('.edit');
        Route::post('/{type}', SaveScheduledPostController::class)->name('.save');

        Route::delete('/{id}', DeleteScheduledPostController::class)
            ->whereUuid('id')
            ->name('.delete');
    });