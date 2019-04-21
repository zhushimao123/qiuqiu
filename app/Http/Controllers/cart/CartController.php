<?php

namespace App\Http\Controllers\cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\model\cart;
use App\model\goods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class CartController extends Controller
{
    //购物车列表
    public function cartlist()
    {
        $cartInfo = cart::where(['uid'=>Auth::id()])->get()->toArray();
        if($cartInfo){
            $zong_price = 0;
            foreach($cartInfo as $k=>$v){
                //商品id == 购物id
                $goodsinfo = goods::where(['g_id'=>$v['goods_id']])->first()->toArray();
                $zong_price = $zong_price +$goodsinfo['goods_price'];
                $cartInfo[]= $zong_price;
            }
        }else{
            header('refresh:2;url=/index');
            exit('购物车为空，2秒后自动跳转商品列表');
        }
        // var_dump($zong_price);
        return view('cart.cartlist',['zong_price'=>$zong_price,'cartInfo'=>$cartInfo]);

    }
}
