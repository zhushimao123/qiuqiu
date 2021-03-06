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

Route::get('shouye','index\IndexController@index');
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
//接受微信服务器推送
Route::post('index', 'goods\GoodsController@wxEven');
//商品详情
Route::get('goodsinfo', 'goods\GoodsController@goodsinfo');
//删除过期的订单
Route::get('orderdel', 'order\OrderController@orderdel');\
//redirect_uri
Route::get('urlencode', function () {
    echo urlencode($_GET['url']);
});
Route::get('code', 'goods\GoodsController@code');      //微信网页授权回调
//生成临时二维码
Route::post('create', 'goods\GoodsController@create');  
Route::get('getimg', 'goods\GoodsController@getimg'); 
//微信菜单
Route::post('creates', 'goods\GoodsController@creates'); 
// //第一从get请求
// Route::get('index', 'kaoshi\GoodsController@index');
// //接受微信服务器推送
// Route::post('index', 'kaoshi\KaoshiController@wxEven');

// Route::get('code', 'kaoshi\KaoshiController@code');      //微信网页授权回调
// //获取access——token数据
// Route::get('token', 'kaoshi\KaoshiController@token');
// //创建用户标签
// Route::post('tags', 'kaoshi\KaoshiController@tags');
// //将指定用户添加至标签
// Route::post('members', 'kaoshi\KaoshiController@members');
// //获取
// Route::get('user', 'kaoshi\KaoshiController@user');

// Route::any('contents', 'kaoshi\KaoshiController@contents');




