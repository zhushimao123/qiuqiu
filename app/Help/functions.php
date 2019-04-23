<?php
use Illuminate\Support\Facades\Redis;
    function ceshi()
    {
        echo 111;
    }
    //获取access_token
    /**
     * jsapi_ticket是公众号用于调用微信JS接口的临时票据。正常情况下，jsapi_ticket的有效期为7200秒，
     * 通过access_token来获取。由于获取jsapi_ticket的api调用次数非常有限，
     * 频繁刷新jsapi_ticket会导致api调用受限，影响自身业务，开发者必须在自己的服务全局缓存jsapi_ticket 。
     */
     function token()
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
     /**
      * 计算签名
      * 用第一步拿到的access_token 采用http GET方式请求获得jsapi_ticket
      *（有效期7200秒，开发者必须在自己的服务全局缓存jsapi_ticket）：
      * https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi
      */
     function jsticket()
     {
        $keys = "jsapi_ticket";
        $jsapi_ticket = Redis::get($keys);
        if($jsapi_ticket){
            return $jsapi_ticket;
        }else{
            $access_token =token();
            // var_dump($access_token);
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $json_date = json_decode(file_get_contents($url),true);
            if(isset($json_date['ticket'])){
                Redis::set($keys,$json_date['ticket']);
                Redis::expire($keys,3600);
                return $json_date['ticket'];
            }else{
                return false;
            }
        }
     }
?>