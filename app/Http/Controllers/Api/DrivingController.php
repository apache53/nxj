<?php
/**
 * Date: 2018/10/2
 * Time: 21:33
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Model\UserBoat;
use App\Library\Utils;
use Cache;
use Config;
use Illuminate\Http\Request;

class DrivingController extends Controller
{
    /************************************************
     * +   报告位置接口
     * /***********************************************/
    public function report(Request $request)
    {
        $out_distance = Utils::safeInput($request->input('out_distance', ''), array("filter_num" => true));
        $latitude = Utils::safeInput($request->input('latitude', ''), array("filter_num" => true));
        $longitude = Utils::safeInput($request->input('longitude', ''), array("filter_num" => true));
        $scenic_id = Utils::safeInput($request->input('scenic_id', ''), array("filter_num" => true));
        $speed = Utils::safeInput($request->input('speed', ''), array("filter_num" => true));
        $distance = Utils::safeInput($request->input('distance', ''), array("filter_num" => true));

        if($latitude>90 && $latitude<-90 || $longitude>180 || $longitude<-180 ){
            Utils::outputJson(11,'经纬度错误',[]);
        }

        if($scenic_id<=0){
            Utils::outputJson(1,'无景点',[]);
        }

        $user = $request->get('user');//中间件产生的参数

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        $driving_data = [
            "latitude" => $latitude,
            "longitude" => $longitude,
            "scenic_id" => $scenic_id,
            "out_distance" => $out_distance,
            "speed" => $speed,
            "distance" => $distance,
        ];

        $res = UserBoat::reportDriving($driving_data,$user,$request_info);

        if($res["error"]==1){
            Utils::outputJson($res["error"],$res["msg"],$res["res"]);
        }
        exit;

    }

    /************************************************
     * +   获取航行位置接口
     * /***********************************************/
    public function lists(Request $request)
    {
        $user_id = Utils::safeInput($request->input('user_id', ''), array("filter_num" => true));
        $drive_day = Utils::safeInput($request->input('drive_day', ''), array("filter_num" => true));

        if(empty($drive_day)){
            $drive_day = date('Ymd');
        }

        $user = $request->get('user');//中间件产生的参数

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        $where = [
            "user_id" => $user_id,
            "drive_day" => $drive_day
        ];
        $res = UserBoat::getList($where);

        $data = [];
        if(!empty($res)){
            foreach($res as $k=>$v){
                if(isset($v->id)){
                    $data[$k] = [
                        "admin_user_id" => $v->admin_user_id,
                        "drive_day" => $v->drive_day,
                        "latitude" => $v->latitude,
                        "longitude" => $v->longitude,
                        "start_latitude" => $v->start_latitude,
                        "start_longitude" => $v->start_longitude,
                        "scenic_id" => $v->scenic_id,
                        "out_distance" => $v->out_distance,
                        "distance" => $v->distance,
                    ];
                }

            }
        }

        Utils::outputJson(1,"ok",$data);
    }
}