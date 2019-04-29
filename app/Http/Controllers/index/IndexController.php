<?php

namespace App\Http\Controllers\index;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\model\goods;
use App\model\goodsdetail;
use App\model\cart;
use Illuminate\Support\Facades\Redis;
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
    //商品详情
    public function goodsdetail()
    {  
        
        $goods_id = intval($_GET['g_id']);
        $key = $goods_id;
        $redis_view_keys = 'ss:goods:view'; //浏览排名
        $history = Redis::incr($key); //商品浏览次数
        Redis::zAdd($redis_view_keys,$history,$goods_id); //添加元素 good_id  次数
        // echo $history;die;
        //数据库做浏览次数
        $res = goods::where(['g_id'=>$goods_id])->first();
        $arr = goodsdetail::where(['goods_id'=>$goods_id])->first();
        // var_dump($arr);die;
        if($arr){
            goodsdetail::where(['goods_id'=>$goods_id])->update(['goods_look'=>$arr['goods_look']+1,'look_time'=>time()]);
        }else{
            $detail = [
                'goods_id'=> $goods_id,
                'goods_name'=> $res['goods_name'],
                'goods_look'=> $arr['goods_look'] +1,
                'uid'=>Auth::id(),
                'look_time'=> time()
            ];
            goodsdetail::insertGetId($detail);
        }
       //缓存商品信息 哈希
       $redis_keys =  'goodsinfo'.$goods_id;
       $cache_info = Redis::hGetAll($redis_keys);
       if($cache_info){
            var_dump($cache_info);
       }else{
             $goods_info =  goods::where(['g_id'=>$goods_id])->first()->toArray();
             Redis::hMset($redis_keys,$goods_info);
       }
       //浏览次数排序  有序集合
       $list1 = Redis::zRangeByScore($redis_view_keys,0,10000,['withscores'=>true]); //正序
    //    echo "<pre>";  print_r($list1); echo "<pre>";
       $list2 = Redis::zRevRange($redis_view_keys,0,10000,true); //倒叙
    //    echo "<pre>";  print_r($list2); echo "<pre>";
        
       $result =  goodsdetail::where(['goods_id'=>$goods_id])->first()->toArray();
       //浏览次数排序
    //    var_dump($result);die;
       $res = [];
           foreach($list2 as $k=>$v)
           {
               $res[] = goodsdetail::where(['goods_id'=>$k])->first();
           }
        //    var_dump($ress);die;
        //浏览历史
        $detailinfo = goodsdetail::orderby('look_time','desc')->get();
        // var_dump($detailinfo);die;
        $server = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        // dd($server);
       return view('goods.lishi',['result'=>$result,'res'=>$res,'detailinfo'=>$detailinfo,'server'=>$server]);
    }
}
