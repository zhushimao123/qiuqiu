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
            $img = $v;
            // var_dump($img);
            return $img;
        }
        // return view('goods.brandlist',['res'=>$res]);
      
     
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
        $data = simplexml_load_string($text);
        // var_dump($data);
        $wx_id = $data-> ToUserName;  //公众号id
        $openid = $data-> FromUserName;//用户的openid
        $Content = $data-> Content; //微信发送的内容
        $CreateTime = $data -> CreateTime; //消息发送的时间
    //    echo $data-> CreateTime;echo "<br>";  //推送时间
        $MsgType = $data-> MsgType;   //消息类型  image  voice 
        $type =  $data-> Event;    //事件类型
        $MediaId = $data -> MediaId;
        // echo $MediaId;
        if($Content =="最新商品"){
            $arr = $this->brandlist();
            // var_dump($arr[]);die;
            // var_dump($arr['goods_img']);die;
            $title = '最新商品';
            // $title = '';
            $goods_name = $arr['goods_name'];
            $img = '/uploads/goodsimg/'.$arr['goods_img'];
            $url = 'www.baidu.com';
            echo '<xml>
            <ToUserName><![CDATA['.$openid.']]></ToUserName>
            <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
            <CreateTime>'.time().'</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <ArticleCount>1</ArticleCount>
            <Articles>
              <item>
                <Title><![CDATA['.$title.']]></Title>
                <Description><![CDATA['.$goods_name.']]></Description>
                <PicUrl><![CDATA['.$img.']]></PicUrl>
                <Url><![CDATA['.$url.']]></Url>
              </item>
            </Articles>
          </xml>';
        
        }
    }
}
