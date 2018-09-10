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
        $password = $this->safeInput($request->input('password', ''), array("filter_sql" => true, "filter_html" => true));
        $username = $this->safeInput($request->input('username', ''), array("filter_sql" => true, "filter_html" => true));
        $vcode = $this->safeInput($request->input('vcode', ''), array("filter_sql" => true, "filter_html" => true));

        if(empty($password) || empty($username)){
            $this->outputJson(11,'信息请填写完整',[]);
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
            $this->outputJson(1,'登录成功',$user["res"]);
        }

        $this->outputJson($user["error"],$user["msg"],[]);
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

}
