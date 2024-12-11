<?php

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'Aimix\Gallery\app\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('gallery', 'GalleryCrudController');
}); // this should be the absolute last line of this file