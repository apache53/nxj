<?php
/**
 * User: xumin
 * Date: 2018/9/9
 * Time: 9:35
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Model\AdminUsers;
use App\Http\Model\Scenic;
use App\Library\AppLogger;
use App\Library\Utils;
use Cache;
use Config;
use Illuminate\Http\Request;

class ScenicController extends Controller
{
    /************************************************
     * +   添加接口
     * /***********************************************/
    public function add(Request $request)
    {
        $scenic_id = Utils::safeInput($request->input('scenic_id', ''), array("filter_num" => true));
        $scenic_name = Utils::safeInput($request->input('scenic_name', ''), array("filter_sql" => true, "filter_html" => true));
        $latitude = Utils::safeInput($request->input('latitude', ''), array("filter_num" => true));
        $longitude = Utils::safeInput($request->input('longitude', ''), array("filter_num" => true));
        $radius = Utils::safeInput($request->input('radius', ''), array("filter_num" => true));
        $pre_id = Utils::safeInput($request->input('pre_id', ''), array("filter_num" => true));

        //echo $scenic_name."#".$latitude."#".$longitude."#".$radius;
        if(empty($scenic_name) ||  empty($latitude) || empty($longitude) || empty($radius)){
            Utils::outputJson(11,'信息请填写完整',[]);
        }

        //景点图片
        $scenic_img = null;
        if($request->hasFile('scenic_img')){
            $scenic_img = $request->file('scenic_img');
            if(!$scenic_img->isValid()){
                $scenic_img = null;
            }
        }

        //景点声音
        $scenic_voice = null;
        if($request->hasFile('scenic_voice')){
            $scenic_voice = $request->file('scenic_voice');
            if(!$scenic_voice->isValid()){
                $scenic_voice = null;
            }
        }

        $user = $request->get('user');//中间件产生的参数

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        $scenic_data = [
            "scenic_id" => $scenic_id,
            "scenic_name" => $scenic_name,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "radius" => $radius,
            "pre_id" => $pre_id,
            "scenic_img" => $scenic_img,
            "scenic_voice" => $scenic_voice
        ];

        if($scenic_id>0){
            //编辑逻辑
            $res = Scenic::editScenic($scenic_data,$user,$request_info);
        }else{
            //新增逻辑
            $res = Scenic::addScenic($scenic_data,$user,$request_info);
        }

        Utils::outputJson($res["error"],$res["msg"],$res["res"]);
    }

    /************************************************
     * +   列表接口
     * /***********************************************/
    public function lists(Request $request)
    {
        $scenic_id = Utils::safeInput($request->input('scenic_id', 0), array("filter_num" => true));

        $where = [
            "scenic_id" => $scenic_id
        ];
        $res = Scenic::getList($where);
        $data = [];
        if(!empty($res)){
            foreach($res as $k=>$v){
                if(isset($v->id)){
                    $data[$k] = [
                        "id" => $v->id,
                        "scenic_name" => $v->scenic_name,
                        "scenic_img" => Utils::getImageUrl($v->scenic_img),
                        "latitude" => $v->latitude,
                        "longitude" => $v->longitude,
                        "voice_path" => Utils::getVoiceUrl($v->voice_path),
                        "radius" => $v->radius,
                        "pre_id" => $v->pre_id,
                    ];
                }

            }
        }

        Utils::outputJson(1,"ok",$data);
    }
}