<?php
/*/**
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
        $scenic_img = $request->input('scenic_img', '');
        $scenic_voice = $request->input('scenic_voice', '');
        $remark = Utils::safeInput($request->input('remark', ''), array("filter_sql" => true, "filter_html" => true));

        //echo $scenic_name."#".$latitude."#".$longitude."#".$radius;
        if(empty($scenic_name) ||  empty($latitude) || empty($longitude) || empty($radius)){
            Utils::outputJson(11,'信息请填写完整',[]);
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
            "scenic_voice" => $scenic_voice,
            "remark" => $remark
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
            $data_sort = $this->sortScenic($res);

            foreach($data_sort as $k=>$v){
                if(isset($v["id"])){
                    /*if($v["pre_id"]>0){
                        $data[$v["pre_id"]]["next_id"] = $v["id"];
                    }*/
                    $data[$k] = $v;
                    if(isset($data_sort[$k+1])){
                        $data[$k]["next_id"] = $data_sort[$k+1]["id"];
                    }else{
                        $data[$k]["next_id"] = 0;
                    }

                }
            }
            /*$data2 = [];
            $i = 0;
            foreach($data as $k=>$v){
                $data2[$i] = $v;
                $i++;
            }
            $data = $data2;*/

        }

        Utils::outputJson(1,"ok",$data);
    }

    /************************************************
     * +   删除接口
     * /***********************************************/
    public function del(Request $request)
    {
        $scenic_id = Utils::safeInput($request->input('scenic_id', 0), array("filter_num" => true));
        if(empty($scenic_id)){
            Utils::outputJson(11,'参数为空',[]);
        }

        $where = [
            "scenic_id" => $scenic_id
        ];
        $res = Scenic::getScenic($where);
        if(is_null($res) || !isset($res->id)){
            return [
                "error"=>12,"msg"=>"景点不存在","res"=>[]
            ];
        }

        $user = $request->get('user');//中间件产生的参数

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        //删除景点
        $res = Scenic::delScenic($res,$user,$request_info);
        Utils::outputJson($res["error"],$res["msg"],$res["res"]);

    }

    //排序
    public function sortScenic($res){
        if(!empty($res)){
            $order = 0;
            $real_order = 0;
            $next_id = 0;
            $data = [];
            $left_res = $res;
            foreach($res as $k=>$v){
                foreach($left_res as $kk=>$vv){
                    if(isset($vv["id"])){
                        if($vv["id"]){
                            if($real_order==0){
                                if($vv["pre_id"]==0){
                                    $data[$order] = $vv;
                                    $order++;
                                    $next_id = $vv["id"];
                                    unset($left_res[$kk]);
                                    break;
                                }
                            }else{
                                if($next_id==$vv["pre_id"]){
                                    $data[$order] = $vv;
                                    $order++;
                                    $next_id = $vv["id"];
                                    unset($left_res[$kk]);
                                    break;
                                }
                            }
                        }

                    }
                }
                $real_order++;
            }
            return $data;
        }
        return [];
    }
}