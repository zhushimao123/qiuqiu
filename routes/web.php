<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('index','index\IndexController@index');
Auth::routes();
//商品详情
Route::get('goodsdetail','index\IndexController@goodsdetail');
//浏览历史
Route::get('lishi','index\IndexController@lishi');
//登陆注册
Route::get('/home', 'HomeController@index')->name('home');
//添加购物车
Route::get('addcart/{goods_id?}', 'index\IndexController@addcart');
//购物车列表
Route::get('cart', 'cart\CartController@cartlist');
//订单
Route::get('order', 'order\OrderController@order');
//订单页
Route::get('orderlist', 'order\OrderController@orderlist');
//订单支付状态
Route::get('paystatus', 'order\OrderController@paystatus');
//微信支付
Route::get('weixin', 'weixin\PayController@weixin');

//微信支付成功回调
Route::post('wxnotify', 'weixin\PayController@wxnotify');   

Route::get('success', 'weixin\PayController@success');      //微信支付成功

//微信JS-SDK //测试
Route::get('tests', 'weixin\test@tests');
//上传的照片
Route::post('getimg', 'weixin\test@getimg'); 
//商品消息
Route::get('brandlist', 'goods\GoodsController@brandlist');  
//第一从get请求
Route::get('index', 'goods\GoodsController@index');
//接受微信服务器的推送
Route::post('wxEven', 'goods\GoodsController@wxEven');