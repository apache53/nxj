<?php

namespace App\Http\Controllers;

use App\Http\Model\H5YunGame;
use App\Http\Model\H5YunPlatform;
use App\Http\Model\H5YunPlatformGame;
use App\Library\AppLogger;
use App\Library\Utils;
use Config;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //检查公共参数
    protected function checkCommonParams(Request $request)
    {
        AppLogger::info('req...');
        $params = array(
            "platform_id" => $this->safeInput($request->input('platform_id', 0), array("filter_num" => true)),
            "game_id" => $this->safeInput($request->input('game_id', 0), array("filter_num" => true)),
            "platform_user_id" => $this->safeInput($request->input('platform_user_id', ''), array("filter_sql" => true, "filter_html" => true)),
            "platform_user_name" => $this->safeInput($request->input('platform_user_name', ''), array("filter_sql" => true, "filter_html" => true)),
            "user_ip" => $this->safeInput($request->input('user_ip', ''), array("filter_sql" => true, "filter_html" => true)),
        );
        if (empty($params['user_ip'])) {
            $params['user_ip'] = Utils::getIP();
        }
        $result = array(
            "error" => 1000,
            "msg" => "",
            "res" => $params
        );
        if (
            empty($params['game_id']) ||
            empty($params['platform_id']) ||
            empty($params['platform_user_id']) ||
            empty($params['platform_user_name']) ||
            empty($params['user_ip'])) {
            $result["error"] = 2001;
            $result["msg"] = "common params empty";
            return $result;
        }

        $game_result = $this->checkGame($params['game_id']);
        if ($game_result["error"] != 1000) {
            $result["error"] = $game_result["error"];
            $result["msg"] = $game_result["msg"];
            return $result;
        }

        $platform_result = $this->checkPlatform($params['platform_id']);
        if ($platform_result["error"] != 1000) {
            $result["error"] = $platform_result["error"];
            $result["msg"] = $platform_result["msg"];
            return $result;
        }

        $platform_game_result = $this->checkPlatformGameRelation($params['platform_id'], $params['game_id']);
        if ($platform_game_result["error"] != 1000) {
            $result["error"] = $platform_game_result["error"];
            $result["msg"] = $platform_game_result["msg"];
            return $result;
        }

        $result["res"]["game"] = $game_result["res"];
        $result["res"]["platform"] = $platform_result["res"];
        $result["res"]["platform_game"] = $platform_game_result["res"];

        return $result;
    }

    //输出结果jsonp
    protected function outputJsonp($error = 1000, $msg = "", $res = array())
    {
        $result = array(
            "error" => $error,
            "msg" => $msg,
            "res" => $res
        );
        $callback = "callback";
        if (isset($_GET["callback"]) && $_GET["callback"]) {
            $callback = $this->safeInput($_GET["callback"], array("filter_html" => true));
        }

        $result = json_encode($result);
        AppLogger::info('rep:' . $result);
        echo $callback . "(" . $result . ")";
        exit;
    }

    //输出结果json
    protected function outputJson($error = 1, $msg = "", $res = array())
    {
        $result = array(
            "error" => $error,
            "msg" => $msg,
            "res" => $res
        );
        $result = json_encode($result);
        AppLogger::info('rep:' . $result);
        echo $result;
        exit;
    }

    //字段安全过滤
    protected function safeInput($str, $filter = array())
    {
        $str = trim($str);
        if (isset($filter["filter_num"]) && $filter["filter_num"]) {
            $str = $str + 0;
        }

        if (isset($filter["filter_sql"]) && $filter["filter_sql"]) {
            $str = addslashes($str);
        }

        if (isset($filter["filter_xss"]) && $filter["filter_xss"]) {
            $str = $this->removeXss($str);
        }

        if (isset($filter["filter_html"]) && $filter["filter_html"]) {
            $str = strip_tags($str);
        }

        return $str;
    }

    //防xss过滤
    protected function removeXss($val)
    {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(�{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
        }
        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);
        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(�{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }
        return $val;

    }
}
