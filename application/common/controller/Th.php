<?php

namespace app\common\controller;
use think\Controller;
use think\facade\Session;

class Th extends Controller
{

    /**
     * 封装返回函数
     * @param $data 返回的值
     * @return data  
     */
    public static function response($data){
        if(!array_key_exists('code', $data)){
            $data['code'] = 20000;
        }
        return json($data);  
    }

}