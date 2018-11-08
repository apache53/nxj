<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Model\AdminUsers;
use App\Http\Model\Vcode;
use App\Library\AppLogger;
use App\Library\Utils;
use Cache;
use Config;
use Illuminate\Http\Request;

class WebController extends Controller
{
    /************************************************
     * +   登录页
     * /***********************************************/
    public function login(Request $request)
    {
        return view('web.login',
            [
                "resource_url" => env("HOST_RESOURCE","")
            ]
        );
    }

    public function scenic(Request $request)
    {
        return view('web.scenic',
            [
                "resource_url" => env("HOST_RESOURCE","")
            ]
        );
    }

}
