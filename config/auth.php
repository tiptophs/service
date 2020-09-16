<?php
/*
 * @Descripttion: 
 * @version: 
 * @Author: tiptop
 * @Date: 2020-08-15 21:42:47
 * @LastEditors: tiptop
 * @LastEditTime: 2020-08-16 13:51:45
 */
// +----------------------------------------------------------------------
// | 用户登录验证
// +----------------------------------------------------------------------
return [
    //有效负载
    'payload'=>[
        'iss'=>'https://www.soln.com',           //签发人
        'aud'=>'https://www.soln.com',           //受众
        'sub'=>'',           //主题
        'exp'=>'',           //过期时间
        'nbf'=>'',           //生效时间
        'iat'=>'',           //签发日期
        'jti'=>''            //编号
    ],
    'primary_key'=>'1gHuiop975cdashyex9Ud23ldsvm2Xq',     //私有key
    //有效时间长度
    'jwt_ttl' => 7 * 24 * 60 * 60,
    //不需要验证的url（白名单）
    'urls'=>[                           
        'whitList'=>[
            'login/*',
            'tool/getTools',
            'tool/getSkills',
        ]
    ]
];
