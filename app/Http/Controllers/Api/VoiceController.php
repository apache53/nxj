<?php
/**
 * User: xumin
 * Date: 2018/12/8
 * Time: 14:50
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Model\UserVoiceLog;
use App\Library\Utils;
use Cache;
use Config;
use Illuminate\Http\Request;

class VoiceController extends Controller
{
    /************************************************
     * +   发送语音接口
     * /***********************************************/
    public function send(Request $request)
    {
        $to_user = Utils::safeInput($request->input('to_user', -2), array("filter_num" => true));
        $voice_time = Utils::safeInput($request->input('voice_time', 0), array("filter_num" => true));
        $user_voice = $request->input('user_voice', '');

        if($to_user<-2 ||  empty($voice_time) || empty($user_voice)){
            Utils::outputJson(11,'参数缺失',[]);
        }

        $user = $request->get('user');//中间件产生的参数

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        $user_voice = str_replace(env('HOST_SELF'),"",$user_voice);
        $voice = [
            "from_user" => $user["admin_user_id"],
            "to_user" => $to_user,
            "voice_path" => $user_voice,
            "voice_time" => $voice_time,
            "create_time" => time(),
            "client_ip" => $request_info["ip"],
            "is_played" => 0,
        ];

        $res = UserVoiceLog::sendVoice($voice,$user,$request_info);

        Utils::outputJson($res["error"],$res["msg"],$res["res"]);
    }

    public function senduserlist(Request $request){
        $user = $request->get('user');//中间件产生的参数

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        $end_time = time();
        $start_time = $end_time-7*86400;
        $data = [
            "from_user" => $user["admin_user_id"],
            "start_time" => $start_time,
            "end_time" => $end_time
        ];
        $res = UserVoiceLog::sendUserList($data,$user,$request_info);

        Utils::outputJson($res["error"],$res["msg"],$res["res"]);
    }

    public function sendlist(Request $request){
        $user = $request->get('user');//中间件产生的参数
        $to_user = Utils::safeInput($request->input('to_user', -2), array("filter_num" => true));

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        $end_time = time();
        $start_time = $end_time-7*86400;
        $data = [
            "from_user" => $user["admin_user_id"],
            "to_user" => $to_user,
            "start_time" => $start_time,
            "end_time" => $end_time
        ];
        $res = UserVoiceLog::sendList($data,$user,$request_info);

        Utils::outputJson($res["error"],$res["msg"],$res["res"]);
    }

    public function receivelist(Request $request){
        $user = $request->get('user');//中间件产生的参数

        $request_info = [
            "ip" => $request->getClientIp()
        ];

        $end_time = time();
        $start_time = $end_time-3600;
        $data = [
            "to_user" => $user["admin_user_id"],
            "start_time" => $start_time,
            "end_time" => $end_time,
            "is_played" => 0,
        ];
        $res = UserVoiceLog::receiveList($data,$user,$request_info);
        UserVoiceLog::voicePlayed($data,$user,$request_info);

        Utils::outputJson($res["error"],$res["msg"],$res["res"]);
    }

}