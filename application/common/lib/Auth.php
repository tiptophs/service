<?php

 namespace app\common\lib;
 
 use app\index\model\User;
 use Firebase\JWT\JWT;
 use think\Db;
 use think\facade\Request;
 use think\facade\Config;
  
 class Auth
 {
  
     /**
      * user
      * @var
      */
     protected $user;
  
    /**
     * 相关jwt配置数据
     */
     protected $config;
  
  
     /**
      * user_id
      * @var
      */
     protected $user_id;
  
     /**
      * @var object 对象实例
      */
     protected static $instance;
  
  
     /**
      * 类架构函数
      * Auth constructor.
      */
     public function __construct()
     {
        if ($config = \think\facade\Config::get('auth.')) {
             $this->config = $config;
         }
     }
  
     /**
      * 初始化
      * @param array $options
      * @return object|static
      */
     public static function instance()
     {
         if (is_null(self::$instance)) {
             self::$instance = new static();
         }
         return self::$instance;
     }
  
     /**
      * 生产token
      * @param $user_id
      * @return string
      */
     public function token($user_id)
     {  
         //获取当前时间
         $time    = time();
         $JWT_TTL = isset($this->config['jwt_ttl']) ? $this->config['jwt_ttl'] : 7 * 24 * 60 * 60;
         $token   = sha1($user_id . uniqid());

         //获取默认的jwt配置数据
         $jwt     = $this->config['payload'];
         
         //设置签发日期
         $jwt['iat'] = $time;
         //设置过期时间
         $jwt['exp'] =  $time + $JWT_TTL;
         //生效日期
         $jwt['nbf'] = $time + '3';

         //添加额外数据
         $jwt['token'] = $token;
         $jwt['user_id'] = $user_id;

         //存储当前token到用户表
         if (Db::name('user')->where('uid', $user_id)->setField('token', $token)) {
            return JWT::encode($jwt, $this->config['primary_key']);
         }
         return '';
     }
  
     /**
      * 校验url，是否需要用户验证
      * @return bool
      */
     public function checkPublicUrl()
     {
        $urls = $this->config['urls'];
        $path = strtolower('/' . str_replace('.', '/', strtolower(Request::module() . '/' .Request::controller() . '/' . Request::action())));
        //判断是否需要验证
         foreach ($urls as $urlTypes) {
             foreach ($urlTypes as $url) {

                $url = strtolower('/'.Request::module() .'/'.$url);
                
                //是否相等 
                if ( $path== $url) return true;

                //是否包含该路径
                if (strpos($path, $url)) return true;

                //匹配为*整个控制器都可以访问
                $str = substr($url, -1);
                if ($str == '*') return true;
             }
         }
        return false;
    }
  
     /**
      * 当前登录用户
      * @param array $jwt
      * @return array|null|\PDOStatement|string|\think\Model
      * @throws \think\db\exception\DataNotFoundException
      * @throws \think\db\exception\ModelNotFoundException
      * @throws \think\exception\DbException
      */
     public function user($user_id)
     {
        if ($user_id) {
             $user = User::where('uid', $user_id)->find();
             unset($user['password']); 
             $this->user    = $user;
             $this->user_id = $user_id;
            
         }
         return $this->user;
     }
  
     /**
      * 清缓存
      * @param $user_id
      */
     public function clear($token)
     {  
        Db::name('user')->where('token', $token)->setField('token', null);
        return true;
         
     }
  
     /**
      * 获取用户id
      * @return mixed
      */
     public function getUserId()
     {
         return $this->user_id;
     }
 }