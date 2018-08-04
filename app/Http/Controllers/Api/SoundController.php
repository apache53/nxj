<?php
/**
 * Created by PhpStorm.
 * User: ak8866hao
 * Date: 2017/11/21
 * Time: 下午1:51
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Model\Sounds;
use App\Http\Model\SoundsListener;
use App\Library\AppLogger;
use App\Library\Utils;
use Cache;
use Config;
use Illuminate\Http\Request;

class SoundController extends Controller
{
    /************************************************
     * +   xxx接口
     * /***********************************************/
    public function getlist(Request $request)
    {
        $params = $request->input();
        $code = isset($params["user"])?$params["user"]:"";
        if(empty($code)){
            $this->outputJson(2,'no code',[]);
        }
        $user = $this->getUser($code);
        if(!isset($user["error"]) || $user["error"] !=1){
            $this->outputJson(3,'error code',[]);
        }

        $openid = $user["data"]["openid"];
        //$openid = 1;
        $where["author"] = $openid;
        $listeners = SoundsListener::getCurrentSoundsListener($where);

        $sound_where["sound_ids"][0] = [0];
        if(count($listeners)>0){
            foreach($listeners as $k=>$v){
                $sound_where["sound_ids"][] = $v->sound_id;
            }
        }
        $sounds = Sounds::getCurrentSounds($sound_where);
        if(isset($sounds->file_path)){
            $listeners_data = [
                "sound_id" => $sounds->sound_id,
                "author" => $openid
            ];
            SoundsListener::addSoundsListener($listeners_data);

            $file_host = 'https://sydemotest.ledu.com';
            $this->outputJson(1,'ok',["file_path"=>$file_host.$sounds->file_path]);
        }

        $this->outputJson(4,'no data',[]);
    }

    public function store(Request $request)
    {
        //判断请求中是否包含name=file的上传文件
        if(!$request->hasFile('file')){
            $this->outputJson(10,'no file',[]);
        }
        $file = $request->file('file');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            $this->outputJson(11,'error file',[]);
        }

        $params = $request->input();
        $code = isset($params["code"])?$params["code"]:"";
        if(empty($code)){
            $this->outputJson(2,'no code',[]);
        }
        $user = $this->getUser($code);
        if(!isset($user["error"]) || $user["error"] !=1){
            $this->outputJson(3,'error code',[]);
        }

        $openid = $user["data"]["openid"];

        // 获取文件相关信息
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $type = $file->getClientMimeType();     // image/jpeg

        // 上传文件
        $filename = date('YmdHis') . '-' . md5($openid)  . rand(100,999) . '.' . $ext;
        // 使用我们新建的uploads本地存储空间（目录）
        $file_dir = "/file/";
        $file_path = $file_dir.$filename;
        file_put_contents(public_path().$file_dir.$filename, file_get_contents($realPath));

        $data = [
            "file_path" => $file_path,
            "file_name" => $filename,
            "author" => $openid
        ];
        Sounds::addSounds($data);
        $this->outputJson(1,'ok',[]);
    }

    private function getUser($code){
        $id = 'wx97fdc823f6364055';
        $secret = '4216907af6e4ef221a34115fe91854a6';

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$id."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code";

        $res = $this->getRequest($url);
        if(!isset($res["session_key"]) || !isset($res["openid"])){
            $result = ['error' => 4010,'msg' => 'login code error'];
            return $result;
        }
        $result = ['error' => 1,'msg' => 'ok','data'=>$res];
        return $result;
    }

    private function getRequest($url){
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_HEADER, 0);
        //curl_setopt($curl_handle, CURLOPT_POST, true);
        //curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $dataArr);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
        $response_json = curl_exec($curl_handle);
        $access_result = json_decode($response_json, true);

        curl_close($curl_handle);

        return $access_result;
    }

}
