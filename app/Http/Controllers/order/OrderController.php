<?php

namespace App\Http\Controllers\order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\model\goods;
use App\model\cart;
use App\model\order;
use App\model\orderdetail;
class OrderController extends Controller
{
    public function order(){
     /*生成订单表
      * 1 计算订单的金额
        2 生成订单号 
      */
      $order_no = time().rand(1111,9999).'_zhushimao';
      $goodsinfo = cart::where(['uid'=>Auth::id()])->get()->toArray();
      // var_dump($goodsinfo);
      $order_amount = 0;
      foreach($goodsinfo as $k=> $v){
        $order_amount = $order_amount + $v['goods_price'];
      }
      $order_info = [
          'uid'=> Auth::id(),
          'create_time' => time(),
          'order_no' => $order_no,
          'order_amount' => $order_amount
      ];
     
      $o_id = order::insertGetId($order_info); 

      //订单详情
      foreach($goodsinfo as $k=> $v){
         $order_detail = [
            'o_id' => $o_id,
            'goods_id' => $v['goods_id'],
            'goods_name' => $v['goods_name'],
            'uid' =>Auth::id()
         ];
         orderdetail::insertGetId($order_detail);
      }

      header('Refresh:3;url=orderlist');
      echo "生成订单成功，3秒将跳转至订单页";
    }
    public function orderlist()
    {
      $res = order::where(['uid'=>Auth::id()])->orderBy('o_id','desc')->get();
      return view('order.orderlist',['res'=>$res]);
    }
    //查询订单支付状态
    public function paystatus(){
      $o_id = intval($_GET['o_id']);
      $info = order::where(['o_id'=>$o_id])->first();
      $response = [];
      if($info){
          if($info->pay_time>0){      //已支付
              $response = [
                  'status'    => 0,       // 0 已支付
                  'msg'       => 'ok'
              ];
          }
          //echo '<pre>';print_r($info->toArray());echo '</pre>';
      }else{
          die("订单不存在");
      }
      die(json_encode($response));
      // echo $o_id;
    }
    //删除过期的订单
    public function orderdel()
    {
        // echo time();die;
        $orderinfo = order::get()->toArray();
        // var_dump($orderinfo);
        foreach($orderinfo as $k=> $v){
            if(time()-$v['create_time'] > 1800 && $v['pay_time'] == 0){
                $update = order::where(['o_id'=> $v['o_id']])->update(['is_del'=>1]);
            }
        }
    }
}
