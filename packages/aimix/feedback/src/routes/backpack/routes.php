<?php

Route::group([
    'namespace' => '',
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'prefix' => config('backpack.base.route_prefix', 'admin'),
], function () {
    $controller = config('backpack.pagemanager.admin_controller_class2', 'Aimix\Feedback\app\Http\Controllers\Admin\FeedbackCrudController');
    //dd($controller);
    Route::crud('feedback', $controller);
});

/*
Route::get('/go', function () {
	dd('go');
});
*/