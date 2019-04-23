<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
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
       $image =  $_GET['img'];
       var_dump($image);
    }
}
