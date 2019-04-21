<?php

namespace App\Http\Controllers\index;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\model\goods;
use App\model\cart;
class IndexController extends Controller
{
    public function index()
    {
        $res = goods::get();
        return view('goods.goodslist',['res'=>$res]);
    }
    //添加商品至购物车
    public function addcart($goods_id)
    {
        if(empty($goods_id)){
            header('refresh:2;url=/index');
            exit('请选择商品，2秒后自动跳转商品列表');
        }
        $goodsinfo = goods::where(['g_id'=>$goods_id])->first();
        // var_dump($goodsinfo);
        if($goodsinfo-> is_show ==0){
            header('refresh:2;url=/index');
            exit('您选择的商品已经下架,2秒后自动跳转商品列表');
        }
        if(!$goodsinfo){
            header('refresh:2;url=/index');
            exit('没有此商品,2秒后自动跳转商品列表');
        }
        //库存
        $cartInfo = cart::where(['goods_id'=>$goods_id])->first();
        if($cartInfo){
            $cart_where = [
                'goods_id' => $goods_id,
                'uid'=>  Auth::id(), //当前登陆的用户id
            ]; 
            //修改
            $info = [
                'buy_num'=>$cartInfo->buy_num + $goodsinfo ->buy_number,
                'create_time'=> time()
            ];
            $update = cart::where($cart_where)->update($info);
            if($update){
                header('refresh:3;url=/cart');
                exit('添加购物车成功，自动跳转至购物车');
            }else{
                header('refresh:3;url=/index');
                exit('添加购物车失败');
            }
        }else{
             //添加
                $cart_info = [
                    'goods_id'=> $goods_id,
                    'buy_num' => $goodsinfo ->buy_number,
                    'goods_name'=> $goodsinfo-> goods_name,
                    'goods_price' => $goodsinfo-> goods_price,
                    'uid' => Auth::id(), //当前登陆的用户id
                    'create_time' => time(),
                    'session_id' => Session::getId() //默认为何服务器存一条唯一的默认的session信息
                ];
                // print_r($cart_info);
                $cart = cart::insertGetId($cart_info);
                if($cart)
                {
                    header('refresh:3;url=/cart');
                    exit('添加购物车成功，自动跳转至购物车');
                }else{
                    header('refresh:3;url=/index');
                    exit('添加购物车失败');
                }
        }
    }
}
