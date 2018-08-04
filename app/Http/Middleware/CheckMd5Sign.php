<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class CheckMd5Sign
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $params = $request->input();
        if(empty($params['time']) || empty($params['sign'])){
            return Response::make(json_encode([
                'error'=>1,
                'msg'=>'time or sign is empty',
                'data'=>[]
            ]));
        }
        $module = config('registry.module');
        $config = config('app.api.'.$module,config('app.api.common',[]));
        $timeout = $config['timeout'];
        $signKey = $config['sign_key'];
        if(!empty($timeout) && abs($params['time'] - time()) > $timeout){
//            return Response::make(json_encode([
//                'error'=>1,
//                'msg'=>'timeout',
//                'data'=>[]
//            ]));
        }
        $sign = $params['sign'];
        unset($params['sign']);
        ksort($params);
        $str = '';
        foreach($params as $k=>$v){
            $str .= "{$k}={$v}&";
        }
        $resign = md5($str.$signKey);
        if(strcasecmp($sign,$resign) !== 0){
            return Response::make(json_encode([
                'error'=>1,
                'msg'=>'sign error',
                'data'=>[]
            ]));
        }
        return $next($request);
    }
}
