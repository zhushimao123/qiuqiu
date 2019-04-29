<?php

namespace App\Http\Controllers\goods;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
class GoodsController extends Controller
{
    public function brandlist()
    {
        // echo 11111;die;
        $res = DB::table('weixin_goods')->where(['goods_new'=>1])->get();
        $arr = json_decode($res,true);
        //  var_dump($arr);die;
        // foreach($arr as $k=>$v){
        //     $img = $v;
        //     // var_dump($img);
            return $arr;
        // }
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
        // dd($text);
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
    //    echo $data-> CreateTime;echo "<br>";  //推送时间
        $MsgType = $data-> MsgType;   //消息类型  image  voice 
        $type =  $data-> Event;    //事件类型
        $MediaId = $data -> MediaId;
        $eventkey = $data -> EventKey;
        // echo $MediaId;
        if($MsgType == 'text'){
            // $arr = $this->brandlist();
            // // var_dump($arr);die;
            // $goodsinfo = [];
            // foreach($arr as $k=>$v){
            //     // var_dump($v['goods_name']);
            //     // $goodsinfo[] = $v['goods_name'];
            //     if($Content == $v['goods_name'])
            //     {
            //         $res = DB::table('weixin_goods')->where(['goods_new'=>1,'goods_name'=>$v['goods_name']])->first();
            //     }
            //  }
            //  var_dump($res);die;
            $where[] = ['goods_name','like',"%$Content%"];
            $res = DB::table('weixin_goods')->where($where)->first();
            if(!empty($res)){
                $title = '商品';
                // $title = '';
                $goods_name = $res->goods_name;
                $img = 'https://1809zhushimao.comcto.com/uploads/goodsimg/'.$res->goods_img;
                $url = 'https://1809zhushimao.comcto.com/goodsinfo?g_id='.$res->goods_id;
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
            }else{
                 $title = '最新商品';
                $goods_name = '无敌小吹风';
                $img = 'https://1809zhushimao.comcto.com/uploads/goodsimg/20190220/4f6e53dccdab7001b7a18359cedf8859.jpg';
                $url = 'https://1809zhushimao.comcto.com/goodsinfo';
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
            $date = [
                'openid'=>$openid,
                'text'=> $Content,
                'text_time'=>$CreateTime

            ];
            $save = DB::table('wx_text')->insert($date);

            if($Content =="最新商品"){
                $arr = $this->brandlist();
               
                // var_dump($arr['goods_img']);die;
                $title = '最新商品';
                // $title = '';
                $goods_name = '无敌小吹风';
                $img = 'https://1809zhushimao.comcto.com/uploads/goodsimg/20190220/4f6e53dccdab7001b7a18359cedf8859.jpg';
                $url = 'https://1809zhushimao.comcto.com/goodsinfo';
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
        }else if($MsgType == 'image'){
            $wx_images_path =  $this->images($MediaId);
            //图片信息入库
            $date = [
               'openid'=>$openid,
               'images'=> $wx_images_path,
               'images_time'=>$CreateTime
            ];
            $info = DB::table('wx_image')->insert($date);
            var_dump($info);
        }else if($MsgType == 'voice'){
            $wx_volices_path =  $this->voices($MediaId);
             $date = [
                'openid'=>$openid,
                'volice'=> $wx_volices_path,
                'volice_time'=>$CreateTime
             ];
             $info = DB::table('wx_volice')->insert($date);
        }

        if($type == 'SCAN')
        {
            $info = [
                'openid'=>$openid,
                'createTime' => $CreateTime,
                'scene_id' => $eventkey,
                'type' => $type

            ];
            $arr = DB::table('wx_scan')->insert($info);
        }else if($type =='subscribe'){
            //根据openid来查是否是唯一用户关注
            $l = DB::table('wx_user')->where(['openid'=>$openid])->first();
            $x= json_encode($l,true);
            $arr = json_decode($x,true);
            // var_dump($arr);die;
            if($arr){ //关注过
                //微信可咦通过 xml 格式来返回给微信用户消息
                $img = $this->brandlist();
                // var_dump($arr[]);die;
                // var_dump($arr['goods_img']);die;
                $title = '欢迎回来';
                // $title = '';
                $goods_name = '帅大大';
                $imgs = 'https://1809zhushimao.comcto.com/uploads/goodsimg/20190220/4f6e53dccdab7001b7a18359cedf8859.jpg';
                $curl = 'https://1809zhushimao.comcto.com/goodsinfo';
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
                    <PicUrl><![CDATA['.$imgs.']]></PicUrl>
                    <Url><![CDATA['.$curl.']]></Url>
                  </item>
                </Articles>
              </xml>';
            }else{
                 //获取用户信息
                $result = $this -> userinfo($openid);
                $ll = $result['openid'];
                //用户信息入库
                $usersinfo = [
                    'openid'=>$ll,
                    'nickname'=> $result['nickname'],
                    'sex'=> $result['sex'],
                    'city'=> $result['city'],
                    'province'=> $result['province'],
                    'headimgurl'=> $result['headimgurl'],
                    'subscribe_time'=> $result['subscribe_time']
                ];
                $insert = DB::table('wx_user')->insert($usersinfo);
                // echo 1;die;
                $img = $this->brandlist();
                // var_dump($arr[]);die;
                // var_dump($arr['goods_img']);die;
                $title = '欢迎关注';
                // $title = '';
                $goods_name = '帅大大';
                $imgs = 'https://1809zhushimao.comcto.com/uploads/goodsimg/20190220/4f6e53dccdab7001b7a18359cedf8859.jpg';
                $curl = 'https://1809zhushimao.comcto.com/goodsinfo';
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
                    <PicUrl><![CDATA['.$imgs.']]></PicUrl>
                    <Url><![CDATA['.$curl.']]></Url>
                  </item>
                </Articles>
              </xml>';
            
            }
             //修改状态
             $where = [
                'openid'=>$openid
            ];
            $status = DB::table('wx_user')->where($where)->update(['sub_status'=>1]);
        }else if($type =='unsubscribe'){
            $where = [
                'openid'=>$openid
            ];
            $status = DB::table('wx_user')->where($where)->update(['sub_status'=>2]);
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
    //微信网页授权
    public function code()
    {
      
        // echo '<pre>';print_r($_GET);echo '</pre>';die;
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
                echo "用户已存在";
               
                // echo  '欢迎'.$reult->nickname.'回来';
                // header('refresh:3;url=/goodsinfo?g_id=3');
                // exit('3秒后，自动跳转至商品详情');
            }
        }else{
            echo  '欢迎'.$user_info['nickname'].'登陆';
            $keys = 'qiuqiu'.$user_info['openid'];
            $user = [
                'time'=> time()
             ];
             Redis::hMset($keys,$user);
             $users = Redis::hGetAll($keys);
             var_dump($users);
            
           
            // $key = time();
            // Redis::zAdd($redis_view_keys,$history,$goods_id);
            // header('refresh:3;url=/goodsinfo?g_id=3');
            // exit('3秒后，自动跳转至商品详情');
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
    //下载图片
    public function images($MediaId)
    {   //调用接口  
        $url =  'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.token().'&media_id='.$MediaId;
        //发送请求
        $client =  new Client();
        $response = $client->get($url);
        // $response=$clinet->request('GET',$url);
        //   var_dump($response);
        //获取文件名
        $file_info = $response->getHeader('Content-disposition'); //数组
        // var_dump($file_info);die;
       $file_name = substr(trim($file_info[0],'"'),-20);
       $new_file_name = rand(1111,9999).'_'.time().$file_name;
        // echo $new_file_name;
       $re= Storage::put('weixin/images/'.$new_file_name,$response->getBody());
    
       $wx_images_path ='weixin/images/'.$new_file_name;
        return  $wx_images_path;
    }
     //下载语音
     public function voices($MediaId)
     {
        
         $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.token().'&media_id='.$MediaId;
      
         $client = new Client();
        
         $response =  $client->get($url);
     
         $file_info = $response->getHeader('Content-disposition'); //数组
       
        $file_name = substr(trim($file_info[0],'"'),-15);
        
        $new_file_name = rand(1111,9999).'_'.time().$file_name;
    
        $res= Storage::put('weixin/volices/'.$new_file_name,$response->getBody());
        
        $wx_volices_path ='weixin/volices/'.$new_file_name;
        return $wx_volices_path;
       
     }
     //临时二维码
     //http请求方式: POST
    //URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN
    public function create()
    {
        $url =  'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.token();
        $json_deta =  [
            'expire_seconds'=> 604800,
            'action_name'=> 'QR_SCENE',
            'action_info'=> [
                'scene'=> [
                    'scene_id'=> 666
                ]
            ]
        ];
        //{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        $json = json_encode($json_deta,true);
        // var_dump($json);
        $client = new Client();
        $response = $client->request('POST',$url,[
            'body' => $json
        ]);
      
        //处理响应
        // echo  $response->getBody();
        $res = $response->getBody();
        $reult = json_decode($res,true);
        // echo "<pre>"; print_r($reult); echo "<pre>";
        $ticket = $reult['ticket'];
        // var_dump($ticket);
        return $ticket;
    }
    //二维码
    public function getimg()
    {
        $tk = $this-> create();
        // echo $ticket;die;
        $ticket = UrlEncode($tk);
        $url =  'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        echo $url;
        // $data = file_get_contents($url);
        // var_dump($data);
    }
    //获取用户信息
    public function userinfo($openid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.token().'&openid='.$openid.'&lang=zh_CN';
        $res =  file_get_contents($url);
        $info = json_decode($res,true);
        return $info;
    }
    //创建微信自定菜单
    public function creates()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.token();
        $post_arr = [
            'button' =>[
                [
                    'type'=> 'view',
                    'name'=> '最新福利',
                    'url'=>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx48451c201710dbcd&redirect_uri=http://1809zhushimao.comcto.com/code&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect"
                ],
                [
                    'type'=> 'view',
                    'name'=> '签到',
                    'url'=>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx48451c201710dbcd&redirect_uri=http://1809zhushimao.comcto.com/code&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect"
                ]
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
}
