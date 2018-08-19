<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
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
            ]
        );
    }

    public function dologin(Request $request)
    {
        $this->outputJson();
    }

    public function vcode(Request $request)
    {
        $vcodeModel = new Vcode();
        $vcodeModel->create_code();
        //exit;
    }

}
