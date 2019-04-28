<?php

namespace App\Admin\Controllers;

use App\model\user;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
class ContentController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {

        $res = user::get();
        // var_dump($_GET);
        if(empty($_GET['name'])){

        }else{
            $name = $_GET['name'];
            $openid = $_GET['openid'];
            // var_dump($openid);
            $open_id = explode(',',$openid);
            // var_dump($opd);
            $result = $this-> sendText($open_id,$name);
        }
        return $content
        ->header('Index')
        ->description('description')
        ->body(view('admin.wxcontent',['res'=>$res]));
    }
    //群发消息
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
