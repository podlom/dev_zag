<?php

Route::group([
    'namespace'  => 'Aimix\Account\app\Http\Controllers\Admin',
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
], function () { 
    Route::crud('usermeta', 'UsermetaCrudController');
    Route::crud('transaction', 'TransactionCrudController');
}); 

