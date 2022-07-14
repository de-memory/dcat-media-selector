<?php

use DeMemory\DcatMediaSelector\Http\Controllers\GroupController;
use DeMemory\DcatMediaSelector\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('media-selector/table', function () {
    return view('de-memory.dcat-media-selector::table');
});

Route::get('media-selector/m-list', MediaController::class . '@list');

Route::post('media-selector/m-upload', MediaController::class . '@upload');

Route::post('media-selector/m-delete', MediaController::class . '@delete');

Route::post('media-selector/m-move', MediaController::class . '@move');


Route::post('media-selector/g-add', GroupController::class . '@add');

Route::post('media-selector/g-delete', GroupController::class . '@delete');

Route::post('media-selector/g-edit', GroupController::class . '@edit');

