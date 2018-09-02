<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Model\AdminUsers;
use App\Http\Model\Vcode;
use App\Library\AppLogger;
use App\Library\Utils;
use Cache;
use Config;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /************************************************
     * +   xxx接口
     * /***********************************************/
    public function login(Request $request)
    {
        return view('backend.index.login',
            [
                "resource_url" => "http://nxj_resource.nxj.cn"
            ]
        );
    }

    public function dologin(Request $request)
    {
        $password = $this->safeInput($request->input('password', ''), array("filter_sql" => true, "filter_html" => true));
        $username = $this->safeInput($request->input('username', ''), array("filter_sql" => true, "filter_html" => true));
        $vcode = $this->safeInput($request->input('vcode', ''), array("filter_sql" => true, "filter_html" => true));

        if(empty($password) || empty($username) || empty($vcode)){
            $this->outputJson(11,'请填写完整',[]);
        }

        $vcodeModel = new Vcode();
        $code_res = $vcodeModel->check_code($vcode);
        if(!$code_res){
            $this->outputJson(12,'验证码错误',[]);
        }

        $user = AdminUsers::checkUser($username,$password);
        if($user["error"] == 1){
            $this->outputJson(1,'登录成功',$user["res"]);
        }

        $this->outputJson($user["error"],$user["msg"],[]);
    }

    public function vcode(Request $request)
    {
        $vcodeModel = new Vcode();
        $vcodeModel->create_code();
        //exit;
    }

}
