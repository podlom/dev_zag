<?php

Route::group([
    'namespace'  => '',
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
], function () { 
    $controller = config('backpack.pagemanager.admin_controller_class2', 'Aimix\Banner\app\Http\Controllers\Admin\BannerCrudController');
    //dd($controller);
    Route::crud('banner', $controller);
});