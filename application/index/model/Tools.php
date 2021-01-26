<?php
/*
 * @Descripttion: 
 * @version: 
 * @Author: tiptop
 * @Date: 2020-08-15 21:42:47
 * @LastEditors: tiptop
 * @LastEditTime: 2020-08-16 23:50:01
 */

namespace app\index\model;

use think\Model;

class Tools extends Model
{

    protected $pk = 'sid'; //默认主键
    protected $table = 'so_tools'; //默认数据表

    protected $autoWriteTimestamp = 'datetime'; //开启自动时间戳，并且设置为datetime格式

    protected $createTime = 'create_time'; //创建时间字段
    protected $updateTime = 'update_time'; //更新时间字段
    protected $dateFormat = 'Y-m-d H:i:s'; //时间字段取出后的默认时间格式

    //状态获取器
    public function getStatusAttr($value)
    {
        $status = ['1' => '启用', '2' => '禁用'];
        return $status[$value];
    }

    public function getModeAttr($value)
    {
        return json_decode($value, true);
    }

    public function getTypeAttr($value)
    {
        return json_decode($value, true);
    }

     //用户状态获取器
    //  public function getDeliveryAttr($value)
    //  {
    //      $status = ['1' => '公开', '2' => '私密'];
    //      return $status[$value];
    //  }
}
