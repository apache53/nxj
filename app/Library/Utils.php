<?php

namespace App\Library;
class Utils
{
    public static function curl_request($url, $post = '', $cookie = '', $returnCookie = 0)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if ($returnCookie) {
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie'] = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        } else {
            return $data;
        }

    }

    public static function getIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function getHttp($http = "")
    {
        if ($http) {
            return $http;
        }
        //$pos = strpos($_SERVER["REQUEST_SCHEME"],'https:');
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            return "https";
        }
        return "http";
    }

    public static function wanbaHttp($login_url, $data, $key)
    {
        $login_host = 'https://api.urlshare.cn';
        $source = 'GET&' . urlencode($login_url);

        ksort($data);
        $source .= '&' . urlencode(http_build_query($data));
        $sign = $key . '&';
        $sig = base64_encode(hash_hmac('sha1', $source, $sign, true));
        $data['sig'] = $sig;
        $url = $login_host . $login_url . '?' . http_build_query($data);
        $res = self::curl_request($url);

        if (!empty($res)) {
            $data = json_decode($res, true);
            if (empty($data)) {
                preg_match('/^[^\(]*\(([\S\s]*)\)/', $res, $matches);
                $data = json_decode($matches[1], true);
            }
        }
        return $data;
    }

    public static function getUrlData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        $content = curl_exec($ch);
        $header = curl_getinfo($ch);
        $arr = parse_url($url);
        if ($header['http_code'] != 302) {
            $res = ['game_content' => $content, 'game_query' => isset($arr['query']) ? $arr['query'] : ''];
            return $res;
        }
        $url = $header['redirect_url'];
        if (strpos($url, 'http') === false) {
            $url = 'http:' . $url;
        }
        return self::getUrlData($url);
    }

    public static function putScriptInHead($file, $script)
    {
        $game_content = '';
        $game_query = '';
        if (isset($file['game_content'])) {
            $game_content = $file['game_content'];
        }
        if (isset($file['game_query'])) {
            $game_query = $file['game_query'];
        }
        echo str_replace('<head>', '<head>' . $script . "<span id='game_query_span' style='display: none'>" . $game_query . "</span>", $game_content);
        exit;
    }

    public static function getYunUserId($yun_user_id)
    {
        $prefix = "mjh5_";
        $user_id = $prefix . $yun_user_id;
        return $user_id;
    }

    public static function curl_post_https($url, $data)
    { // 模拟提交数据函数
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据，json格式
    }

    public static function request($host, $data, $method = "get", $timeout = 5, $is_https = false, $is_json = false)
    {
        $ch = curl_init();
        if ($is_json) {
            $params = json_encode($data);
        } else {
            if (is_array($data)) {
                $params = http_build_query($data);
            } else {
                $params = $data;
            }

        }

        $url = $host . "?" . $params;

        // 2. 设置选项，包括URL
        if ($method == 'get') {
            curl_setopt($ch, CURLOPT_URL, $url);
        } elseif ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $host);
            //curl_setopt($ch, CURLOPT_POST,count($data)) ; // 启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        if ($is_https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if ($is_json) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($params))
            );
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);
        return $output;
    }

    public static function compareFloat($arg1, $arg2)
    {
        if (abs($arg1 - $arg2) < pow(10, -5)) {
            return true;
        }
        return false;
    }

    public static function getSafeString($string){
        return strip_tags(addslashes(trim($string)));
    }

    public static function getRandChar($length = 8){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }

}