<?php

namespace app\index\controller;

use app\common\controller\Th;
use think\facade\Request;
use think\facade\Validate;
use app\index\model\User;
use think\facade\Session;
use app\common\lib\Auth;

class Login extends Th{

    /**
     * 登录操作
     */
    public function login(){
        if(Request::post()){      //判断当前是否为post提交数据
            $data = Request::param();
            $res = User::get(function($query) use ($data){
                $query->where('email', $data['account'])
                      ->where('password', sha1($data['password']));
            });
            if($res === null){
                return Th::response([
                        'prompt' => '邮箱或密码不正确，请您检查...',
                        'status' => false
                    ]);
            }else{
                $jwt = app()->auth->token($res->uid);
                app()->user = app()->auth->user($res->uid);
                return Th::response([
                    'status' => true, 
                    'data'=>['name'=>$res->name, 'uid'=>$res->uid, 'jwt'=>$jwt]]
                );
            }
        }else{
            return Th::response([
                'status'=> true,
                'prompt' => '您的请求是非法的...'
            ]);
        }
    }

    /**
     * 退出操作
     */
    public function loginOut(){
        $token = Request::post('token');
        app()->auth->clear($token);
        return Th::response($ret);
    }


    //+---------------------
    //|     用户注册部分
    //+---------------------

    /**
     * 用户注册数据的方法
     * @return \think\response\Json
     */
    public function register(){
        if(Request::isPost()){
            $data = Request::post();
            $check = $this->checkRegister($data);
            $data['uid'] = $this->getUserNumber();      //自动生成用户编号
            if($check['status']){                       //添加数据
                unset($data['confirm_password']);
                $user = User::create($data, true);
                if($user){
                    //存储用户信息到session
                    Session::set('user', array('name'=>$user->name, 'uid'=>$user->uid));
                    return Th::response(['status' => true, 'user'=>['name'=>$user->name, 'uid'=>$user->uid]]);
                }else{
                    return Th::response(['status'=>false, 'prompt'=>'数据存储失败，请稍后重试...']);
                }
            }else{                          //返回格式错误提示信息
                return Th::response(['status'=>false, 'prompt'=>$check['message']]);
            }
        }else{
            return Th::response(['status'=>false, 'prompt'=>'您的请求是非法的...']);
        }
    }




    //+-----------------------
    //|        独立验证部分
    //+-----------------------

    /**
     * 验证登录数据信息
     * @param $data
     * @return array
     */
    protected function checkRegister($data){
        //设置返回数组
        $ret = array('status'=>true);

        //定义验证字段
        $rule = [
            'name|昵称'=>[
                'require' => 'require',
                'max'     => '10',
                'chsAlphaNum' => 'chsAlphaNum'          //仅允许汉字/字母/数字
            ],
            'email|邮箱'=>[
                'require' => 'require',
                'email'   => 'email',
                'unique'  => 'user'
            ],
            'phone|手机号码'=>[
                'require' => 'require',
                'mobile'  => 'mobile',
                'unique'  => 'user'
            ],
            'password|密码'=>[
                'require' => 'require',
                'min'     => '6',
                'max'     => '20',
                'alphaNum' => 'alphaNum',                //仅允许数字和字母
                'confirm' => 'confirm_password'
            ]
        ];

        //初始化验证过则
        Validate::rule($rule);
        //验证数据
        if(!Validate::check($data)){
             $ret['message'] = Validate::getError();
             $ret['status'] = false;
        }
        return $ret;
    }


    /**
     * 登录字段验证
     * @param $data     //需要验证的数据
     * @return array
     */
    protected function checkLoginParam($data){
        //设置默认返回数据
        $ret = array('status'=>true);
        return $ret;
    }


    /**
     * 自动生成随机账号
     * @return string
     */
    private function getUserNumber()
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $username = "";
        for ( $i = 0; $i < 6; $i++ )
        {
            $username .= $chars[mt_rand(0, strlen($chars))];
        }
        return strtoupper(base_convert(time() - 1420070400, 10, 16)).$username;
    }




    //+---------------------------
    //|         测试函数
    //+---------------------------

    public function test(){
        echo 'this controller is ok';
    }



}