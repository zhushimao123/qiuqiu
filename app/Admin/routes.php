<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('goods', GoodsController::class);
    //订单管理
    $router->resource('orderinfo', OrderController::class);
    //微信用户管理
    $router->resource('users', UserController::class);
    //素材 文字
    $router->resource('info', TextController::class);
     //素材  图片
     $router->resource('image', ImageController::class);
      //素材 语音
    $router->resource('volice', VoliceController::class);
    //_____________________________________________________
    //新增临时素材
    $router->any('wximg', 'WximgController@index');
    //消息也
    $router->any('contents', 'ContentController@index');
});
