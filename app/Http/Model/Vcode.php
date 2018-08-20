<?php


namespace App\Http\Model;

use App\Library\VerifyCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class Vcode
{
    public function create_code(){
        $VerifyCode = new VerifyCode();

        $font = array(
            'space' => 0,
            'size' => 14,
        );

        $VerifyCode->setFont($font);

        $VerifyCode->generateCode();
        $verify_code = strtolower($VerifyCode->getVerifyCode());

        //$ip = Request::getClientIp();
        $key = uniqid();
        $cookie_key = Cookie::get('image_verifycode_key');

        $image_verifycode_key = $cookie_key ? $cookie_key : md5($verify_code.$key . rand(1000000,9999999)) ;

        Cache::put($image_verifycode_key, $verify_code,6);

        setcookie("image_verifycode_key", $image_verifycode_key, time()+60 * 5, "/");
        //Cookie::make('image_verifycode_key', $image_verifycode_key, 5);

        //Header("Content-type: image/PNG");

        return $VerifyCode->paint();
    }

    public  function check_code($code){

        $cookie_key = isset($_COOKIE["image_verifycode_key"])?$_COOKIE["image_verifycode_key"]:"";//Cookie::get('image_verifycode_key');

        if(empty($cookie_key)){
            return false;
        }
        $cache_code = Cache::get($cookie_key);
        if(strtolower($code) == $cache_code) {
            return true;
        } else {
            return false;
        }
    }
}
