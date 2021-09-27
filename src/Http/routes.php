<?php

use DeMemory\DcatMediaSelector\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::post('media-selector/add-group', Controllers\DcatMediaSelectorController::class . '@addGroup');

Route::post('media-selector/media-move', Controllers\DcatMediaSelectorController::class . '@move');

Route::get('media-selector/media-list', Controllers\DcatMediaSelectorController::class . '@getMediaList');

Route::post('media-selector/media-upload', Controllers\DcatMediaSelectorController::class . '@upload');

Route::post('media-selector/media-delete', Controllers\DcatMediaSelectorController::class . '@delete');


