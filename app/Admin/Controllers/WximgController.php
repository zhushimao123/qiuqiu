<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class WximgController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content, Request $request)
    {
           
        // var_dump(token());die;
            $img=$this->upload($request,'filename');
            // var_dump($img);die;
            if(!$img){
                echo '';
            }else{
              
                $url ='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.token().'&type=image';
                // var_dump($url);die;
                $client = new Client();
                $response =  $client->request('POST', $url, [
                    'multipart' => [
                        [
                            'name'     => 'filename',
                            'contents' => fopen('../storage/app/'.$img, 'r')
                        ],
                    ]
                ]);
            //    echo $response->getBody();
               $res = $response -> getBody();
               $json_data = json_decode($res,true);
               var_dump($json_data);
            //    die;
            //入库
                $info = [
                    'type'=> $json_data['type'],
                    'media_id'=> $json_data['media_id'],
                    'created_at'=> $json_data['created_at'],
                    'img'=> $img
                ];
                $result = DB::table('wx_updimg')->insert($info);

            }
          
           
<<<<<<< HEAD
            //入库 控制器相当于   index.php    /找strol   ../strol
=======
            //入库 控制器相当于   index.php    /找strol   ../strol    
>>>>>>> 860e7099e50df6b3447bb9f22c4737ea6ce2aee2
     
             
        return $content
        ->header('Index')
        ->description('description')
        ->body(view('admin.wximg'));
    }
    
    /**文件上传 */
    public function upload(Request $request,$filename){
        if ($request->hasFile($filename) && $request->file($filename)->isValid()) {
            $photo = $request->file($filename);
            // $extension = $photo->extension();
            // $store_result = $photo->store('photo');
            $store_result = $photo->store('uploads/'.date('Ymd'));
            return $store_result;
        }
        // exit('未获取到上传文件或上传过程出错');
    }

}
