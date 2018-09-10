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
}
