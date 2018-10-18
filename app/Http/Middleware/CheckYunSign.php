<?php

namespace App\Http\Middleware;

use App\Http\Model\AdminUsers;
use App\Library\AppLogger;
use App\Library\Utils;
use Closure;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class CheckYunSign
{
    /**
     * 云接口的验证机制.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $no_login = $this->getNoLoginList();
        $no_role = $this->getNoRoleList();
        $role_list = $this->getRoleList();
        $route_path = $request->path();
        $req_data = $request->input();
        AppLogger::info('req:' . var_export($req_data,true));
        if(in_array($route_path,$no_login)){
            return $next($request);
        }

        $login_token = Utils::safeInput($request->input('login_token', ''), array("filter_sql" => true, "filter_html" => true));
        if(empty($login_token)){
            Utils::outputJson(101,'请先登录',[]);
        }

        $request_info = [
            "ip" => $request->getClientIp(),
        ];
        $res = AdminUsers::checkToken($login_token,$request_info);
        if($res["error"] != 1){
            Utils::outputJson($res["error"],$res["msg"],[]);
        }

        $user = $res["res"];
        if(in_array($route_path,$no_role)){
            $request->attributes->add(["user"=>$user]);
            return $next($request);
        }

        if(isset($role_list[$user["role_id"]]) && in_array($route_path,$role_list[$user["role_id"]])){
            $request->attributes->add(["user"=>$user]);
            return $next($request);
        }

        Utils::outputJson(102,"无权限",[]);
    }

    //不需要登录的请求
    public function getNoLoginList(){
        return [
            "api/sound/getlist",
            "api/sound/store",
            "api/scenic/list",
            "api/user/login"
        ];
    }

    //不需要角色判断的请求
    public function getNoRoleList(){
        return [
            "api/user/info",
            "api/user/logout",
            "api/file/upload",
            "api/driving/report"
        ];
    }

    //不同角色对应的权限
    public function getRoleList(){
        return [
            1=>[
                "api/scenic/add",
                "api/user/list",
                "api/user/add",
                "api/user/del",
                "api/driving/list",
                "api/scenic/del",
                "api/file/scenic_upload",
            ],
            2=>[

            ]
        ];
    }
}
