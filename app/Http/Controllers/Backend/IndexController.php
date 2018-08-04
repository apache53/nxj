<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
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
        echo 'login';
        //header('location:client://loadgame|http://s20103.dl.game.ledu.com?client=1&server_name=双线103服');exit;
    }

}
