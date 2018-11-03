<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        //$response = $next($request);
        /*$response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, multipart/form-data, application/json');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        $response->header('Access-Control-Allow-Credentials', 'false');*/
        /*$headers = [
            'Access-Control-Allow-Origin' => 'http://test.test.com',
            'Access-Control-Allow-Credentials' => 'false',
            'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Cache-Control, Authorization',
            'Access-Control-Allow-Methods' => 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS, TRACE',
        ];
        $response->headers->add($headers);*/

        return $next($request);
    }
}
