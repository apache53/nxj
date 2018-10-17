<?php
/**
 * Created by PhpStorm.
 * User: ak8866hao
 * Date: 2017/11/21
 * Time: 下午1:51
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Model\AdminUsers;
use App\Http\Model\Vcode;
use App\Library\AppLogger;
use App\Library\Utils;
use Cache;
use Config;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /************************************************
     * +   登录接口
     * /***********************************************/
    public function login(Request $request)
    {
        $password = Utils::safeInput($request->input('password', ''), array("filter_sql" => true, "filter_html" => true));
        $username = Utils::safeInput($request->input('username', ''), array("filter_sql" => true, "filter_html" => true));
        $vcode = Utils::safeInput($request->input('vcode', ''), array("filter_sql" => true, "filter_html" => true));
        $login_type = Utils::safeInput($request->input('login_type', ''), array("filter_sql" => true, "filter_html" => true));

        if(empty($password) || empty($username)){
            Utils::outputJson(11,'信息请填写完整',[]);
        }

        /*$vcodeModel = new Vcode();
        $code_res = $vcodeModel->check_code($vcode);
        if(!$code_res){
            $this->outputJson(12,'验证码错误',[]);
        }*/

        $request_info = [
            "ip" => $request->getClientIp()
        ];
        $user = AdminUsers::checkUser($username,$password,$request_info);
        if($user["error"] == 1){
            if($login_type!=$user["res"]["role_id"]){
                Utils::outputJson(15,'账号类型不正确',$user["res"]);
            }
            Utils::outputJson(1,'登录成功',$user["res"]);
        }

        Utils::outputJson($user["error"],$user["msg"],[]);
    }

    /************************************************
     * +   获取用户信息接口
     * /***********************************************/
    public function info(Request $request)
    {
        $user = $request->get('user');//中间件产生的参数
        Utils::outputJson(1,'成功',$user);
    }

    /************************************************
     * +   用户退出登录接口
     * /***********************************************/
    public function logout(Request $request)
    {
        $login_token = Utils::safeInput($request->input('login_token', ''), array("filter_sql" => true, "filter_html" => true));

        if(empty($login_token)){
            Utils::outputJson(11,'参数为空',[]);
        }

        $request_info = [
            "ip" => $request->getClientIp()
        ];
        $user = AdminUsers::logout($login_token,$request_info);
        if($user["error"] == 1){
            Utils::outputJson(1,'成功',$user["res"]);
        }

        Utils::outputJson($user["error"],$user["msg"],[]);
    }

    /************************************************
     * +   用户列表接口
     * /***********************************************/
    public function lists(Request $request)
    {
        $user = $request->get('user');
        $user_name = Utils::safeInput($request->input('user_name', ''), array("filter_sql" => true, "filter_html" => true));
        $real_name = Utils::safeInput($request->input('real_name', ''), array("filter_sql" => true, "filter_html" => true));
        $role_id = Utils::safeInput($request->input('role_id', ''), array("filter_num" => true));

        $where = [
            "user_name" => $user_name,
            "real_name" => $real_name,
            "role_id" => $role_id,
        ];
        $res = AdminUsers::getList($where);
        $data = [];
        if(!empty($res)){
            foreach($res as $k=>$v){
                if(isset($v->admin_user_id)){
                    $head_img = !empty($v->head_img)?$v->head_img:"//file/image/head_default.png";
                    $data[$k] = [
                        "user_id" => $v->admin_user_id,
                        "user_name" => $v->user_name,
                        "real_name" => $v->real_name,
                        "user_mobile" => $v->user_mobile,
                        "role_id" => $v->role_id,
                        "head_img" => Utils::getImageUrl($head_img)
                    ];
                }

            }
        }

        Utils::outputJson(1,"ok",$data);
    }

    /************************************************
     * +   用户添加编辑接口
     * /***********************************************/
    public function add(Request $request)
    {
        $user_id = Utils::safeInput($request->input('user_id', ''), array("filter_num" => true));
        $user_name = Utils::safeInput($request->input('user_name', ''), array("filter_sql" => true, "filter_html" => true));
        $real_name = Utils::safeInput($request->input('real_name', ''), array("filter_sql" => true, "filter_html" => true));
        $user_mobile = Utils::safeInput($request->input('user_mobile', ''), array("filter_sql" => true, "filter_html" => true));
        $role_id = Utils::safeInput($request->input('role_id', ''), array("filter_num" => true));
        $head_img = $request->input('head_img', '');
        $password = $request->input('password', '');

        if(empty($real_name) || empty($role_id) || !in_array($role_id,[1,2])){
            Utils::outputJson(11,'信息请填写完整',[]);
        }

        $user = $request->get('user');//中间件产生的参数

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        $user_data = [
            "user_id" => $user_id,
            "user_name" => $user_name,
            "real_name" => $real_name,
            "user_mobile" => $user_mobile,
            "role_id" => $role_id,
            "head_img" => $head_img,
            "password" => $password,
        ];

        if($user_id>0){
            //编辑逻辑
            $res = AdminUsers::editUser($user_data,$user,$request_info);
        }else{
            //新增逻辑
            $res = AdminUsers::addUser($user_data,$user,$request_info);
        }

        Utils::outputJson($res["error"],$res["msg"],$res["res"]);
    }

    /************************************************
     * +   删除用户接口
     * /***********************************************/
    public function del(Request $request)
    {
        $user_id = Utils::safeInput($request->input('user_id', ''), array("filter_num" => true));

        if(empty($user_id)){
            Utils::outputJson(11,'参数为空',[]);
        }

        $where = [
          "admin_user_id" => $user_id
        ];
        $user_res = AdminUsers::getUser($where);

        if(is_null($user_res) || !isset($user_res->admin_user_id)){
            return [
                "error"=>12,"msg"=>"用户不存在","res"=>[]
            ];
        }

        $user = $request->get('user');//中间件产生的参数

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        //删除景点
        $res = AdminUsers::delUser($user_res,$user,$request_info);
        Utils::outputJson($res["error"],$res["msg"],$res["res"]);
    }

}
