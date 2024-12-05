<?php

use EntrieCloud\LaravelCockpit\Http\Controllers;

Route::group(['prefix' => config('cockpit.cache_clear_path')], function () {
    Route::any('/{secrets}', Controllers\LaravelCockpitClearController::class)
        ->where('secrets', '.*')
        ->name('cockpit.clear');
});
