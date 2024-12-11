<?php

Route::group([
    'namespace'  => '',
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
], function () { 
    $controller = config('backpack.pagemanager.admin_controller_class2', 'Aimix\Review\app\Http\Controllers\Admin\ReviewCrudController');
    //dd($controller);
    Route::crud('review', $controller);
}); 

