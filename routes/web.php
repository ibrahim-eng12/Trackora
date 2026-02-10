<?php

use Illuminate\Support\Facades\Route;
use IbrahimEng12\Trackora\Http\Controllers\TrackoraController;

Route::group([
    'prefix' => config('trackora.route_prefix', 'trackora'),
    'middleware' => array_merge(
        config('trackora.middleware', ['web', 'auth']),
        ['trackora.authorize']
    ),
    'as' => 'trackora.',
], function () {
    Route::get('/', [TrackoraController::class, 'index'])->name('dashboard');
    Route::get('/export', [TrackoraController::class, 'export'])->name('export');
    Route::post('/clear', [TrackoraController::class, 'clear'])->name('clear');
    Route::post('/purge', [TrackoraController::class, 'purge'])->name('purge');
    Route::get('/stats', [TrackoraController::class, 'stats'])->name('stats');
});
