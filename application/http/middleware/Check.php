<?php
/*
 * @Descripttion: 
 * @version: 
 * @Author: tiptop
 * @Date: 2020-08-15 21:42:47
 * @LastEditors: tiptop
 * @LastEditTime: 2020-08-16 21:26:47
 */
namespace app\http\middleware;

use app\common\lib\Auth;
use Firebase\JWT\JWT;
use think\exception\HttpException;
use think\facade\Config;

class Check{
   
    public function handle($request, \Closure $next){
       
        // JWT 验证登录
        $auth = Auth::instance();
        
        //获取token认证数据
        $jwt = $request->header('Authorization');
        
        //设置默认用户为我的账户
        $user = $auth->user('8AC4E8DG918B1');
        unset($user['token']);
        unset($user['phone']);

        if(!is_null($jwt)){
            try {
                $jwt = (array)JWT::decode($jwt, Config::get('auth.primary_key'), ['HS256']);
                if ($jwt && $jwt['exp'] > time()) {         //返回对象并且时间有效
                    $user = $auth->user($jwt['user_id']);
                }
            } catch (\Exception $e) {
                $jwt = null;
            }
        }
    
        //注入用户
        app()->user = $user;
        app()->auth = $auth;
        
        
        //检查访问权限
        if (!$auth->checkPublicUrl()) {
            if (empty($jwt)) {
                return json(['code'=>50008, 'prompt'=>'未授权访问']);
            }
            if (!$user) {
                return json(['code'=>50014, 'prompt'=>'登录已过期，请重新登录']);
            }
            if ($user['token'] != $jwt['token']) {
                return json(['code'=>50012, 'prompt'=>'用户验证失败，请重新登录']);
            }
        }
        return $next($request);
    }
    
}
