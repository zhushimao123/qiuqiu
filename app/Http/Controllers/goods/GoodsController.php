<?php

namespace App\Http\Controllers\goods;
use Illuminate\Support\Str;
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
            $img = 'http://1809zhushimao.comcto.com/uploads/goodsimg/20190220/4f6e53dccdab7001b7a18359cedf8859.jpg';
            $url = 'http://1809zhushimao.comcto.com/goodsinfo';
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
        if($MsgType == 'text'){
            echo 111;
        }
    }
    public function goodsinfo()
    {
        $res = DB::table('weixin_goods')->where(['goods_new'=>1])->orderby('create_time','desc')->limit(1)->get();
       
         $jsticket = jsticket();
     //计算签名
        $noncestr=Str::random(10);
        $jsapi_ticket=$jsticket;
        $timestamp=time();
        $url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        // var_dump($url);die;
        //排序
        $string1 = 'jsapi_ticket='.$jsapi_ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
        $string2 = sha1($string1);
        // echo $string2;die;
        $info =[
            'appId' => 'wx48451c201710dbcd',
            'timestamp'=> $timestamp,
            'noncestr' => $noncestr,
            'signature' => $string2
        ];
        return view('goods.brandlist',['res'=>$res,'info'=>$info]);
    }
    public function code()
    {
      
        // echo '<pre>';print_r($_GET);echo '</pre>';
        //2 通过code换取网页授权access_token
        $code = $_GET['code'];
        /**
         * 获取code后，请求以下链接获取access_token：  
         * https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID
         * &secret=SECRET&code=CODE&grant_type=authorization_code
         * 
         */
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx48451c201710dbcd&secret=f583f90f3aed8ec33ae6dd30eceebe5f&code='.$code.'&grant_type=authorization_code';
        $json_data = json_decode(file_get_contents($url),true);
        // echo '<pre>';print_r($json_data);echo '</pre>';
        $access_token = $json_data['access_token'];
        // var_dump($access_token);die;
        $openid = $json_data['openid'];
        /**
         *    4 拉取用户信息
         *https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
         */
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
             //入库
            $info = [
                'openid'=> $user_info['openid'],
                'nickname'=> $user_info['nickname'],
                'sex'=> $user_info['sex'],
                'city'=> $user_info['city'],
                'province'=> $user_info['province'],
                'country'=> $user_info['country'],
                'headimgurl' => $user_info['headimgurl'],
            ];
            // var_dump($info);
            $res = DB::table('wx_user')->insert($info);
        }
    }
}
