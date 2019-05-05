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
    //获取access_token 
    public function token()
    {
       $key = "access_token";
       $access_token = Redis::get($key);
       if($access_token){
           return $access_token;
       }else{
           $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx48451c201710dbcd&secret=f583f90f3aed8ec33ae6dd30eceebe5f'; 
           $json_data = json_decode(file_get_contents($url),true);
           // var_dump($json_data);   
           if(isset($json_data['access_token'])){
               Redis::set($key,$json_data['access_token']);
               Redis::expire($key,3600);
               return $json_data['access_token'];
           }else{
               return false;
           }
       }
    }
    //创建用户标签
    public function tags()
    {
       $url = 'https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.token();
        // {   "tag" : {     "name" : "广东"//标签名   } } 数据格式
        $post_arr = [
            'tag' =>[
                'name'=> "北京2"
            ]
        ];
        //格式JSON
        $json = json_encode($post_arr,JSON_UNESCAPED_UNICODE);
        $client = new Client();
        //发送请求
        $response = $client->request('POST',$url,[
            'body' => $json
        ]);
        $res = $response->getBody();
        echo $res;
    }
    //将指定用户添加到标签
    public function members()
    {
        $url  ='https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.token();
            // $post_arr = [
            //     'openid_list' =>[
            //        "oafqt5gB1TnlWA0dxKpvy9DdP8jQ",
            //        "oafqt5owvCDO5iok6z7QKMh5fm1Q"  ],
            //        "tagid" => 134
            // ];
           $post_arr = '{  
                "openid_list" : [   
                "oafqt5tccgLDT5LMnqxBQH6kSwE4",    
                "oafqt5owvCDO5iok6z7QKMh5fm1Q"   ],   
                "tagid" : 100
             }';
              //格式JSON
        // $json = json_encode($post_arr,JSON_UNESCAPED_UNICODE);
        $client = new Client();
        //发送请求
        $response = $client->request('POST',$url,[
            'body' => $post_arr
        ]);
        $res = $response->getBody();
        echo $res;
    }
    //获取标签下大的粉丝数
    //{   "tagid" : 134,   "next_openid":""//第一个拉取的OPENID，不填默认从头开始拉取 }
    //接口get https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=ACCESS_TOKEN
   public function user()
   {
       $url = 'https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token='.token();
       $post_arr ='{  
        "tagid" : 100,
        "next_openid":""
         }';
         $client = new Client();
        //发送请求
        $response = $client->request('POST',$url,[
            'body' => $post_arr
        ]);
        $res = $response->getBody();
        // echo $res;
        $json = json_decode($res,true);
        // var_dump($res);die;
        $data = [];
        foreach($json as $k=> $v){
            // var_dump($k);
           $data [] = $v;
        }
        // var_dump($data);
        $openid1 = $data[1]['openid'][0];
        $openid2 = $data[1]['openid'][1];
        $openid3 = $data[1]['openid'][2];
        //获取标签
        //  $url2 = 'https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.token(); 
        //  $res=   file_get_contents($url);
        //  var_dump($res);die;
    
        return view('weixin.open',['openid1'=>$openid1,'openid2'=>$openid2,'openid3'=> $openid3]);
   }
   //消息群发
   public function contents()
   {
        if(empty($_GET['name'])){

        }else{
            $key = "name";
            $name = Redis::get($key);
            if($name){
                
            }else{
                $name = $_GET['name'];
                if($name){
                    Redis::set($key,$name);
                    return $name;
                }else{
                    return false;
                }
            }

           
            $openid = $_GET['openid'];
            // var_dump($openid);die;
            $open_id = explode(',',$openid);
            // var_dump($opd);
            $result = $this-> sendText($open_id,$name);
        }
   }
    //处理
    public function sendText($open_id,$name)
    {
          //接口
       $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.token();
       $text = [
           'touser'=> $open_id,
           'msgtype'=> 'text',
           'text'=> [
               'content'=> $name
           ]
       ];
       $json = json_encode($text,JSON_UNESCAPED_UNICODE);//处理中文
    //    var_dump($json);die;
       //发送请求
       $client = new Client();

       $response = $client->request('POST',$url,[
           'body' => $json
       ]);
     
       //处理响应
       echo  $response->getBody();
    }


}
