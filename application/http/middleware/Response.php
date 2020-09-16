<?php
namespace app\http\middleware;

use app\common\lib\Auth;
use Firebase\JWT\JWT;
use think\exception\HttpException;
use think\facade\Config;

class Response{
   
    public function handle($request, \Closure $next){
       
        $response = $next($request);
        // 添加中间件执行代码
        
        return $response;
        
    }
    
}
