<?php

namespace App\Http\Controllers\goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class GoodsController extends Controller
{
    public function brandlist()
    {
        // echo 11111;die;
        $res = DB::table('weixin_goods')->where(['goods_new'=>1])->orderby('create_time','desc')->limit(5)->get();
        $arr = json_decode($res,true);
        //  var_dump($arr);die;
        foreach($arr as $k=>$v){
            $img = $v['goods_img'];
        }
        // return view('goods.brandlist',['res'=>$res]);
        return $img;
    }
    //第一次get请求
    public function index()
    {
        echo $_GET['echostr'];
    }
    /**
     *接受微信的推送事件
     */
    public function wxEven()
    {
        //接受微信服务器推送
        $text = file_get_contents('php://input');
        $time = date('Y-m-d H:i:s');
        $str = $time . $text . "\n";
        is_dir('logs') or mkdir('logs', 0777, true);
        file_put_contents("logs/wx_event.log", $str, FILE_APPEND);
    //     $data = simplexml_load_string($text);
    //     $wx_id = $data-> ToUserName;  //公众号id
    //     $openid = $data-> FromUserName;//用户的openid
    //     $Content = $data-> Content; //微信发送的内容
    //     // echo $Content;
   
    //     $CreateTime = $data -> CreateTime; //消息发送的时间
    //     // echo $CreateTime;
    // //    echo $data-> CreateTime;echo "<br>";  //推送时间
    //     $MsgType = $data-> MsgType;   //消息类型  image  voice 
    //     // echo $MsgType;
    //     // echo  $content;echo "<br>";
    //     $type =  $data-> Event;    //事件类型
    //     $MediaId = $data -> MediaId;
    //     // echo $MediaId;
    }
}
