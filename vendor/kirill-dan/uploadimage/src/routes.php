<?php

// Paths for ajax functions.
Route::group(['prefix' => 'ajax', 'namespace' => 'Dan\UploadImage\Controllers'], function () {

    // Save image from WYSIWYG editor.
    Route::post('uploader/upload', 'UploadImageController@upload');

    // Delete image from WYSIWYG editor.
    Route::post('uploader/delete', 'UploadImageController@delete');

    // Create preview image for form.
    Route::post('uploader/preview', 'UploadImageController@preview');
});