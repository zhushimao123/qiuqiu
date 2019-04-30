<?php

namespace App\Http\Controllers\kaoshi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
class KaoshiController extends Controller
{
    //微信配置信息第一次get请求
    public function index()
    {
        echo $_GET['echostr'];
    }
    //接受微信服务器推送
    public function wxEven()
    {
        $text = file_get_contents('php://input');
        $time = date('Y-m-d H:i:s');
        $str = $time . $text . "\n";
        is_dir('logs') or mkdir('logs', 0777, true);
        file_put_contents("logs/wx_event.log", $str, FILE_APPEND);
        $data = simplexml_load_string($text);
        // var_dump($data);die;
        $wx_id = $data-> ToUserName;  //公众号id
        $openid = $data-> FromUserName;//用户的openid
        $Content = $data-> Content; //微信发送的内容
        $CreateTime = $data -> CreateTime; //消息发送的时间
        $MsgType = $data-> MsgType;   //消息类型  image  voice 
        $type =  $data-> Event;    //事件类型
        $MediaId = $data -> MediaId;
        $eventkey = $data -> EventKey;
    }
    //微信网页授权
    public function code()
    {
        /**
         * https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx48451c201710dbcd&redirect_uri=http://1809zhushimao.comcto.com/code&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
         */
        // echo '<pre>';print_r($_GET);echo '</pre>';die;
        //2 通过code换取网页授权access_token
        $code = $_GET['code'];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx48451c201710dbcd&secret=f583f90f3aed8ec33ae6dd30eceebe5f&code='.$code.'&grant_type=authorization_code';
        $json_data = json_decode(file_get_contents($url),true);
        // echo '<pre>';print_r($json_data);echo '</pre>';
        $access_token = $json_data['access_token'];
        // var_dump($access_token);die;
        $openid = $json_data['openid'];
        //拉取用户信息
        $url2= 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_info = json_decode(file_get_contents($url2),true);
        // echo '<pre>';print_r($user_info);echo '</pre>';die;
        $reult = DB::table('wx_user')->where(['openid'=>$user_info['openid']])->first();
        
        if($reult){
            if($user_info['openid'] == $reult->openid){
                echo  '欢迎'.$reult->nickname.'回来';
            }
        }else{
            echo  '欢迎'.$user_info['nickname'].'登陆';
            $info = [
                'openid'=> $user_info['openid'],
                'nickname'=> $user_info['nickname'],
                'sex'=> $user_info['sex'],
                'city'=> $user_info['city'],
                'province'=> $user_info['province'],
                'country'=> $user_info['country'],
                'headimgurl' => $user_info['headimgurl'],
            ];

            $res = DB::table('wx_user')->insert($info);
        }

    }
}
