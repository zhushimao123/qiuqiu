<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
class test extends Controller
{
    public function tests()
    {
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
        return view('weixin.tests',['info'=>$info]);
    }
    //上传的图片
    public function getimg()
    {
        $MediaId = file_get_contents('php://input');
        // var_dump($text);
        $url =  'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.token().'&media_id='.$MediaId;
        // var_dump($url);
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
    }
}
